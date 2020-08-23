<?php

namespace Mips\HydraoClient\Api;

use Mips\Http\HttpClient;
use Mips\HydraoClient\Model\ShowerHeadModel;
use Mips\HydraoClient\Model\ShowerHeadShowerModel;
use Mips\HydraoClient\Model\ShowerHeadStatsModel;

class ShowerHead extends AbstractApi {

    private $deviceUUID;

    public function __construct(HttpClient $client, string $deviceUUID) {
        parent::__construct($client);
        $this->deviceUUID = $deviceUUID;
    }

    public function get() {
        $this->client->getLogger()->debug("GET shower-heads/{$this->deviceUUID}");
        return new ShowerHeadResult($this->client->doGet("shower-heads/{$this->deviceUUID}"));
    }

    public function showers($limit = null, $fromid = null) {
        $this->client->getLogger()->debug("GET shower-heads/{$this->deviceUUID}/showers");

        $data = array();
        if (is_numeric($limit)) {
            $data['limit'] = $limit;
        }
        if (is_numeric($fromid)) {
            $data['fromid'] = $fromid;
        }

        return new ShowerHeadShowersResult($this->client->doGet("shower-heads/{$this->deviceUUID}/showers", $data));
    }

    public function stats($nbShowers = null) {
        $this->client->getLogger()->debug("GET shower-heads/{$this->deviceUUID}/stats");

        $data = array();
        if (is_numeric($nbShowers)) {
            $data['nbShowers'] = $nbShowers;
        }

        return new ShowerHeadStatsResult($this->client->doGet("shower-heads/{$this->deviceUUID}/stats", $data));
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

class ShowerHeadShowersResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = array();
        foreach ($json as $shower) {
            $this->data[] = new ShowerHeadShowerModel($shower);
        }
    }

    /**
     * @return ShowerHeadShowerModel[]
     */
    public function getData(): array {
        return $this->data;
    }

}

class ShowerHeadStatsResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = new ShowerHeadStatsModel($json);
    }

    public function getData(): ShowerHeadStatsModel {
        return $this->data;
    }

}