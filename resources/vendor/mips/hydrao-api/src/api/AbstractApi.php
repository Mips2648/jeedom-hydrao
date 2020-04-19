<?php

namespace mips\hydraoapi\api;

use mips\hydraoapi\Client;

abstract class AbstractApi {
    protected $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }
}