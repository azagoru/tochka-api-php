<?php

namespace App\Services\TochkaApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Request extends GuzzleClient
{
    private $url;
    private $method;
    private $params;
    private $auth;

    public function __construct($url, $method, $params, $auth = [])
    {
        parent::__construct(['defaults' => [
            'headers' => [
                'user-agent' => Config::USER_AGENT . phpversion() . '/' . Config::WRAPPER_VERSION
            ]
        ]]);

        $this->url      = $url;
        $this->method   = $method;
        $this->auth     = $auth;

        if ($method === 'POST')
            $this->params   = $params;
    }

    /**
     * Trigger the actual call
     * @param $call
     * @return Response the call response
     */
    public function call()
    {
        $response = null;

        try {
            if ($this->params)
                $requestData['json'] = $this->params;

            if ($this->auth) {
                $requestData['headers'] = [
                    'Authorization' => "Bearer {$this->auth['access_token']}"
                ];
            }

            $response = call_user_func_array(
                [$this, strtolower($this->method)], [$this->url, $requestData]
            );
        }
        catch (ClientException $e) {
            $response = $e->getResponse();
        } catch (ServerException $e) {
            $response = $e->getResponse();
        }

        return new Response($this, $response);
    }

    

    /**
     * Http method getter
     * @return string Request method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Call Url getter
     * @return string Request Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Call Params getter
     * @return string Request Url
     */
    public function getParams() {
        return $this->params;
    }


}

// ссылка для получения authorization_code, вводится вручную в браузер, после — авторизация в ЛК,
// далее выбор опций и редирект с получением кода
// код действует 2 минуты, используем для получения токенов
// https://enter.tochka.com/api/v1/authorize?response_type=code&client_id=CqV56y5dAytMOjR5JgRMkXORIvmiN8oj

// {"refresh_token":"EvEo4QXZBjasaKdQ4DHXDozeYCHBlz3T","token_type":"bearer","access_token":"tKZ5HpiRLIU09VRRgbZELEaYcWLKFw01","expires_in":86400}