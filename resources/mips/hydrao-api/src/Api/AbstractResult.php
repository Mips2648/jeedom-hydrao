<?php

namespace Mips\HydraoClient\Api;

use Mips\Http\HttpResponse;

/**
 * Result request Api
 */
abstract class AbstractResult {

    /**
     * @var HttpResponse
     */
    private $httpResponse;

    protected $data;

    abstract protected function loadData($json);
    abstract public function getData();

    /**
     * @param HttpResponse $response
     */
    public function __construct(HttpResponse $response) {
        $this->httpResponse = $response;

        $body = $this->getResponseBody();

        if ($this->isSuccess() && $body != '') {
            $jsonData = json_decode($body, false);
            $this->loadData($jsonData);
        }
    }

    /**
     * @return string
     */
    public function getResponseBody() {
        return $this->httpResponse->getBody();
    }

    /**
     * Contains the values of status codes defined for HTTP.
     * @return int
     */
    public function getHttpStatusCode() {
        return $this->httpResponse->getHttpStatusCode();
    }

    /**
     * Get Error
     * @return string
     */
    public function getHttpError() {
        return $this->httpResponse->getError();
    }

    /**
     * Gets a value that indicates if the HTTP response was successful.
     * @return bool
     */
    public function isSuccess() {
        return $this->httpResponse->isSuccess();
    }
}
