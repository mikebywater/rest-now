<?php

namespace Now\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Auth
{
    /**
     * Default max retries for retry middleware
     */
    CONST DEFAULT_MAX_RETRIES = 5;

    /**
     * Default max delay between retries in seconds
     */
    CONST DEFAULT_MAX_DELAY_BETWEEN_RETRIES_IN_SECONDS = 60;

    /**
     * @var HandlerStack
     */
    protected HandlerStack $handlerStack;

    public $client;
    public $options;

    /**
     * Auth constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->buildRetryHandler();
        $this->client = new \GuzzleHttp\Client([
            'handler' => $this->handlerStack,
            // URL for access_token request
            'base_uri' => $config->base_uri,
            'Connection' => 'close',
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
        ]);

        $this->options = ['form_params' =>
            [
                'grant_type' => 'password',
                'client_id' => $config->client_id,
                'client_secret' => $config->client_secret,
                'username' => $config->username,
                'password' => $config->password,
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        // Try and retrieve a valid oauth token
        $cachedToken = Cache::get('servicenow_oauth_token');

        // It may have expired, or doesnt exist. Access tokens last for 30 minutes
        if ($cachedToken == null) {
            $response = $this->client->post('/oauth_token.do', $this->options);
            $decodedResponse = json_decode($response->getBody());
            $cachedToken = $decodedResponse->access_token;
            Cache::put('servicenow_oauth_token', $cachedToken, now()->addSeconds($decodedResponse->expires_in));
        }

        return $cachedToken;
    }

    protected function buildRetryHandler()
    {
        $this->handlerStack = HandlerStack::create();
        $this->handlerStack->push(Middleware::retry($this->shouldAttemptRetry(),  $this->setDelay()));
    }

    protected function shouldAttemptRetry()
    {
        $maxRetries = config('http_client.max_retries') ?? self::DEFAULT_MAX_RETRIES;

        return function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null,
            \Exception $exception = null
        ) use ($maxRetries) {
            $doRetry = $retries < $maxRetries
                && ($exception instanceof \Exception || ($response && $response->getStatusCode() >= 400));

            if ($doRetry) {
                $uri = $request->getUri();
                Log::warning('Retrying request', [
                    'retry_attempt' => $retries + 1,
                    'uri' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?' . $uri->getQuery(),
                    'body' => $request->getBody()->getContents(),
                ]);
            }

            return $doRetry;
        };
    }

    protected function setDelay()
    {
        $maxDelayBetweenRetriesInSeconds = config('http_client.max_delay_between_retries_in_seconds')
            ?? self::DEFAULT_MAX_DELAY_BETWEEN_RETRIES_IN_SECONDS;

        return function ($retries) use ($maxDelayBetweenRetriesInSeconds) {
            return min(($maxDelayBetweenRetriesInSeconds * 1000), RetryMiddleware::exponentialDelay($retries));
        };
    }
}
