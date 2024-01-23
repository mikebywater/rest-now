<?php

namespace Now\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Auth
{
    const DEFAULT_INCREMENTAL_RETRY_IS_ACTIVE = false;
    const DEFAULT_MAX_RETRIES = 5;
    const DEFAULT_MAX_DELAY_BETWEEN_RETRIES_IN_SECONDS = 60;

    protected HandlerStack $handlerStack;
    public Client $client;
    public array $options;

    /**
     * Auth constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->buildHandler();

        $this->client = new Client([
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

    protected function buildHandler()
    {
        $this->handlerStack = HandlerStack::create();
        $incrementalRetryIsActive = config('http_client.incremental_retry_is_active') ?? self::DEFAULT_INCREMENTAL_RETRY_IS_ACTIVE;
        if ($incrementalRetryIsActive) {
            $this->handlerStack->push(Middleware::retry($this->shouldAttemptRetry(), $this->setDelay()));
        }
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
