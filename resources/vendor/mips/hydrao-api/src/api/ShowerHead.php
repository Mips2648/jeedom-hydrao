<?php

namespace mips\hydraoapi\api;

use mips\hydraoapi\Client;
use mips\hydraoapi\model\ShowerHeadModel;
use mips\hydraoapi\model\ShowerModel;

class ShowerHead extends AbstractApi {

    private $deviceUUID;

    public function __construct(Client $client, string $deviceUUID) {
        parent::__construct($client);
        $this->deviceUUID = $deviceUUID;
    }

    public function index() {
        $this->client->getLogger()->debug("GET shower-heads/{$this->deviceUUID}");
        return new ShowerHeadResult($this->client->executeRequest('GET', "shower-heads/{$this->deviceUUID}"));
    }

    public function shower($limit = null, $fromid = null) {
        $this->client->getLogger()->debug("GET shower-heads/{$this->deviceUUID}/showers");

        $data = array();
        if (is_numeric($limit)) {
            $data['limit'] = $limit;
        }
        if (is_numeric($fromid)) {
            $data['fromid'] = $fromid;
        }

        return new ShowerResult($this->client->executeRequest('GET', "shower-heads/{$this->deviceUUID}/showers"));
    }
}

class ShowerHeadResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = new ShowerHeadModel($json);
    }

    public function getData(): ShowerHeadModel {
        return $this->data;
    }

}

class ShowersResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = array();
        foreach ($json as $shower) {
            $this->data[] = new ShowerModel($shower);
        }
    }

    /**
     * @return ShowerModel[]
     */
    public function getData(): array {
        return $this->data;
    }

}