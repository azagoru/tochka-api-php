<?php

namespace Azagoru\TochkaApiPHP;

class Client
{
    private $baseUri = Config::BASE_URI;
    private $version = Config::VERSION;

    private $clientId;
    private $secret;

    private $refresh_token;
    private $access_token;

    /**
     * Client constructor requires:
     * @param array  $keys
     */
    public function __construct(array $keys)
    {
        $this->clientId  = $keys['TOCHKA_CLIENT_ID'];
        $this->secret    = $keys['TOCHKA_CLIENT_SECRET'];

        $this->setAuthentication($keys);
    }

    /**
     * Set auth
     * @param array $keys
     */
    private function setAuthentication($keys)
    {
        if (isset($keys['refresh_token']))
            $keys = $this->getTokens($keys['refresh_token'], 'refresh_token');
        else if (isset($keys['authorization_code']))
            $keys = $this->getTokens($keys['authorization_code'], 'authorization_code');

        $this->refresh_token = $keys['refresh_token'] ?? null;
        $this->access_token  = $keys['access_token'] ?? null;
    }

    /**
     * Trigger a POST request
     * @param array $resource Azagoru Resource/Action pair
     * @param array $params Request params
     * @param int $auth
     * @return Response
     */
    public function post($resource, $params, $auth = 1)
    {
        if ($auth)
            $auth = ['access_token' => $this->access_token];

        $url = $this->buildURL($resource, 'POST');

        $request = new Request($url, 'POST', $params, $auth);

        return $request->call();
    }

    /**
     * Trigger a POST request
     * @param array $resource Azagoru Resource/Action pair
     * @param array $params Request params
     * @param int $auth
     * @return Response
     */
    public function get($resource, $params, $auth = 1)
    {
        if ($auth)
            $auth = ['access_token' => $this->access_token];

        $url = $this->buildURL($resource, 'GET', $params);

        $request = new Request($url, 'GET', $params, $auth);

        return $request->call();
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    private function getTokens($key, $grantType)
    {
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->secret,
            'grant_type'    => $grantType
        ];

        if ($grantType === 'refresh_token')
            $params['refresh_token'] = $key;

        if ($grantType === 'authorization_code')
            $params['code'] = $key;

        $result = $this->post(Resources::$Token, $params, 0);

        return $result->getBody();
    }

    /**
     * Build the final call url without query strings
     * @param string $resource Mailjet resource
     * @param string $method
     * @param array $params Request params
     * @return string final call url
     */
    private function buildUrl($resource, $method, $params = [])
    {
        $path = $this->baseUri . 'api/' . $this->version . '/' . join('/', $resource);

        if ( ($method === 'GET') && isset($params['request_id']) )
            $path .= '/' . $params['request_id'];

        return $path;
    }
}