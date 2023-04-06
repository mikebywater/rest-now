<?php

namespace Now\Client;

use Illuminate\Support\Facades\Cache;

class Auth
{
    public $client;
    public $options;

    /**
     * Auth constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {

        $this->client = new \GuzzleHttp\Client([
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
                            ] ];
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
}
