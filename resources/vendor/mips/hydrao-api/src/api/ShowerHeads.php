<?php

namespace mips\hydraoapi\api;

use mips\hydraoapi\model\ShowerHeadModel;

class ShowerHeads extends AbstractApi {

    public function index() {
        $this->client->getLogger()->debug('GET shower-heads');
        return new ShowerHeadsResult($this->client->executeRequest('GET', 'shower-heads'));
    }

    private $ShowerHead;

    public function showerHead($deviceUUID) {
        $this->client->getLogger()->debug("build ShowerHead");
        return $this->ShowerHead ?: ($this->showerheads = new \hydraoapi\api\ShowerHead($this->client, $deviceUUID));
    }
}

class ShowerHeadsResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = array();
        foreach ($json as $ShowerHead) {
            $this->data[] = new ShowerHeadModel($ShowerHead);
        }
    }

    /**
     * @return ShowerHeadModel[]
     */
    public function getData(): array {
        return $this->data;
    }

}