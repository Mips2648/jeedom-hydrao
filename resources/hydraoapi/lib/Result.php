<?php

namespace hydraoapi;

/**
 * Result request API
 */
class Result {

    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var array
     */
    private $response;

    /**
     * @var string
     */
    private $error;

    /**
     * @param array $response
     * @param int $httpCode
     * @param string  $error
     */
    public function __construct($httpCode, $response = array(), $error='') {
        $this->httpStatusCode = (int)$httpCode;
        $this->response = $response;
        $this->error = $error;
    }

    /**
     * @return array
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Contains the values of status codes defined for HTTP.
     * @return int
     */
    public function getHttpStatusCode() {
        return $this->httpStatusCode;
    }

    /**
     * Get Error
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Gets a value that indicates if the HTTP response was successful.
     * @return bool
     */
    public function isSuccess() {
        return $this->httpStatusCode == 200;
    }

}
