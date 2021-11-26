<?php

namespace Mips\HydraoClient\Api;

use Mips\Http\HttpClient;

abstract class AbstractApi {
    protected $client;

    public function __construct(HttpClient $client) {
        $this->client = $client;
    }
}
