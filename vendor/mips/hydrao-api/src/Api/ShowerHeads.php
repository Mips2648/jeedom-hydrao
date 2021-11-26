<?php

namespace Mips\HydraoClient\Api;

use Mips\HydraoClient\Model\ShowerHeadModel;

class ShowerHeads extends AbstractApi {

    public function get() {
        $this->client->getLogger()->debug('GET shower-heads');
        return new ShowerHeadsResult($this->client->doGet('shower-heads'));
    }

    /**
     * @var ShowerHead
     */
    private $ShowerHead;

    public function showerHead($deviceUUID) {
        $this->client->getLogger()->debug("build ShowerHead");
        return $this->ShowerHead ?: ($this->showerheads = new ShowerHead($this->client, $deviceUUID));
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
