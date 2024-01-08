<?php

namespace Now\Client;

class RestApi extends Table
{
    const CONTENT_TYPE_JSON = 'application/json';
    const QUERY_STRING_SEPARATOR = '&';

    public function restApiGet($nameSpace, $apiName, $endPointName, $queryParameters, $headers = [])
    {
        $url = $this->buildUrl($nameSpace, $apiName, $endPointName, $queryParameters);
        $response = $this->client->get($url, ['headers' => array_merge($this->getHeaders(), $headers)]);

        return json_decode($response->getBody());
    }

    public function restApiPost(
        $nameSpace,
        $apiName,
        $endPointName,
        $queryParameters = [],
        $data = [],
        $headers = ['Content-Type' => self::CONTENT_TYPE_JSON]
    ) {
        $url = $this->buildUrl($nameSpace, $apiName, $endPointName, $queryParameters);
        $response = $this->client->post($url, ['headers' => array_merge($this->getHeaders(), $headers), 'json' => $data]);

        return json_decode($response->getBody());
    }

    public function restApiPatch(
        $nameSpace,
        $apiName,
        $endPointName,
        $queryParameters = [],
        $data = [],
        $headers = ['Content-Type' => self::CONTENT_TYPE_JSON]
    ) {
        $url = $this->buildUrl($nameSpace, $apiName, $endPointName, $queryParameters);
        $response = $this->client->patch($url, ['headers' => array_merge($this->getHeaders(), $headers), 'json' => $data]);
        return json_decode($response->getBody());
    }

    public function buildQueryString($queryParameters): string
    {
        if (empty($queryParameters)) {
            return '';
        }
        return '?' . http_build_query($queryParameters, '', self::QUERY_STRING_SEPARATOR);
    }

    private function buildUrl(string $nameSpace, string $apiName, string $endPointName, array $queryParameters): string
    {
        return sprintf('/api/%s/%s/%s%s', $nameSpace, $apiName, $endPointName, $this->buildQueryString($queryParameters));
    }
}
