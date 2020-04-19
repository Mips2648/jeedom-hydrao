<?php

namespace Mips\Http;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Mips HttpClient
 * @author Mips
 */
class HttpClient {

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $host;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @param string $host
     * @return HttpClient
     */
    public function __construct(string $_host, LoggerInterface $logger = null) {
        $this->host = $_host;
        $this->headers[] = 'Content-Type: application/json';
        $this->logger = $logger ?: new NullLogger();

        if ($this->host[strlen($this->host) - 1] === '/') {
            $this->host = substr($this->host, 0, strlen($this->host) - 1);
        }
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function getLogger() {
        return $this->logger;
    }

    /**
     * @param string $_method must be 'GET', 'POST', 'PUT' or 'DELETE'
     * @param string $_path
     * @param array  $_data
     * @param array  $_headers
     * @throws \InvalidArgumentException
     * @return HttpResponse
     */
    public function executeRequest(string $_method, string $_path, array $_data = [], array $_headers = []) {
        $method = strtoupper($_method);

        if (! in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) throw new InvalidArgumentException("Method not supported:{$method}");

        //remove null data
        $requestData = array_filter($_data, function ($value) {
            return $value !== null;
        });
        $requestHeaders = array_filter($_headers, function ($value) {
            return $value !== null;
        });

        $url = "{$this->host}/$_path";

        $countdata = count($requestData);
        $countheaders = count($requestHeaders);
        $this->logger->debug("preparing request {$method} {$url} nb_data:{$countdata} nb_headers:{$countheaders}");

        $requestHeaders = array_merge($this->headers, $requestHeaders);

        $ch = curl_init();
        if ($method === 'GET' && count($requestData)) {
            $url .= '?' . http_build_query($requestData);
        } elseif ($method != 'GET') {
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            }
            $content = json_encode($requestData);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            $requestHeaders[] = 'Content-Length: ' . strlen($content);
            unset($content);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

        $this->logger->debug("sending request...");
        $curlResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        unset($ch);
        $this->logger->debug("result {$httpCode}");

        return new HttpResponse($httpCode, $curlResponse, $error);
    }

}