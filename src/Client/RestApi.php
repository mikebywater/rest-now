<?php

namespace Now\Client;

class RestApi extends Table
{

    public function restApiGet($nameSpace, $apiName, $endPointName, $queryParameters, $headers = [])
    {
        $response = $this->client->get(
            '/api/' .
            $nameSpace . '/' .
            $apiName . '/' .
            $endPointName .
            $this->getQueryString($queryParameters),
            ['headers' => array_merge($this->getHeaders(), $headers)]
        );

        return json_decode($response->getBody());
    }

    public function restApiPost(
        $nameSpace,
        $apiName,
        $endPointName,
        $queryParameters = [],
        $data = [],
        $headers = ['Content-Type' => 'application/json']
    ) {
        $response = $this->client->post(
            '/api/' .
            $nameSpace . '/' .
            $apiName . '/' .
            $endPointName .
            $this->getQueryString($queryParameters),
            ['headers' => array_merge($this->getHeaders(), $headers), 'form_params' => $data]
        );

        return json_decode($response->getBody());
    }

    public function getQueryString($queryParameters)
    {
        if (empty($queryParameters)) {
            return '';
        }
        $queryString = '?';
        $operator = '';
        foreach ($queryParameters as $queryParameter => $queryValue) {
            $queryString = $queryString . $operator . $queryParameter . '=' . $queryValue;
            $operator = '&';
        }
        return $queryString;
    }
}
