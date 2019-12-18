<?php

namespace Azagoru\TochkaApiPHPWrapper;
use Psr\Http\Message\ResponseInterface;

class Response
{
    private $status;
    private $success;
    private $body;
    private $rawResponse;

    public function __construct($request, $response)
    {
        $this->request = $request;

        if ($response) {
            $this->rawResponse = $response;
            $this->status = $response->getStatusCode();
            $this->body = $this->decodeBody($response->getBody());
            $this->success = floor($this->status / 100) == 2 ? true : false;
        }
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getData()
    {
        if (isset($this->body['Data'])) {
            return $this->body['Data'];
        }

        return $this->body;
    }

    public function getCount()
    {
        if (isset($this->body['Count'])) {
            return $this->body['Count'];
        }

        return null;
    }

    public function getReasonPhrase()
    {
        return $this->rawResponse->getReasonPhrase();
    }

    public function getTotal()
    {
        if (isset($this->body['Total'])) {
            return $this->body['Total'];
        }

        return null;
    }

    public function success()
    {
        return $this->success;
    }

    protected function decodeBody($body)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            /** In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
             * to specify that large ints (like Steam Transaction IDs) should be treated as
             * strings, rather than the PHP default behaviour of converting them to floats.
             */
            $object = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        } else {
            /** Not all servers will support that, however, so for older versions we must
             * manually detect large ints in the JSON string and quote them (thus converting
             *them to strings) before decoding, hence the preg_replace() call.
             */
            $maxIntLength = strlen((string) PHP_INT_MAX) - 1;
            $jsonWithoutBigIntegers = preg_replace('/:\s*(-?\d{'.$maxIntLength.',})/', ': "$1"', $body);
            $object = json_decode($jsonWithoutBigIntegers, true);
        }
        return $object;
    }
}
