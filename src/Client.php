<?php

namespace Azagoru\TochkaApiPHPWrapper;

class Client
{
    private $baseUri = Config::BASE_URI;
    private $version = Config::VERSION;

    private $clientId;
    private $secret;

    private $refresh_token;
    private $access_token;

    public function __construct(array $keys)
    {
        $this->clientId  = getenv('TOCHKA_CLIENT_ID');
        $this->secret    = getenv('TOCHKA_CLIENT_SECRET');

        $this->setAuthentication($keys);
    }

    private function setAuthentication($keys)
    {
        if (isset($keys['refresh_token']))
            $keys = $this->getTokens($keys['refresh_token'], 'refresh_token');
        else if (isset($keys['authorization_code']))
            $keys = $this->getTokens($keys['authorization_code'], 'authorization_code');

        // Log::debug(json_encode($keys));

        $this->refresh_token = $keys['refresh_token'] ?? null;
        $this->access_token  = $keys['access_token'] ?? null;
    }

    public function post($resource, $params, $auth = 1)
    {
        if ($auth)
            $auth = ['access_token' => $this->access_token];

        $url = $this->buildURL($resource, 'POST');

        $request = new Request($url, 'POST', $params, $auth);

        return $request->call();
    }

    public function get($resource, $params, $auth = 1)
    {
        if ($auth)
            $auth = ['access_token' => $this->access_token];

        $url = $this->buildURL($resource, 'GET', $params);

        $request = new Request($url, 'GET', $params, $auth);

        return $request->call();
    }

    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

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

    private function buildUrl($resource, $method, $params = [])
    {
        $path = $this->baseUri . 'api/' . $this->version . '/' . join('/', $resource);

        if ( ($method === 'GET') && isset($params['request_id']) )
            $path .= '/' . $params['request_id'];

        return $path;
    }
}

// ссылка для получения authorization_code, вводится вручную в браузер, после — авторизация в ЛК,
// далее выбор опций и редирект с получением кода
// код действует 2 минуты, используем для получения токенов
// https://enter.tochka.com/api/v1/authorize?response_type=code&client_id=CqV56y5dAytMOjR5JgRMkXORIvmiN8oj

// {"refresh_token":"EvEo4QXZBjasaKdQ4DHXDozeYCHBlz3T","token_type":"bearer","access_token":"tKZ5HpiRLIU09VRRgbZELEaYcWLKFw01","expires_in":86400}