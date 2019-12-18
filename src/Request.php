<?php

namespace Azagoru\TochkaApiPHP;

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