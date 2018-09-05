<?php

namespace Now\Client;

class Auth
{
    public $client;

    /**
     * Auth constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {

        $this->client = new \GuzzleHttp\Client([
            // URL for access_token request
            'base_uri' => $config->base_uri,
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
        $response = $this->client->post('/oauth_token.do', $this->options );
        $token = json_decode($response->getBody())->access_token;
        return $token;
    }


}