<?php

namespace Mips\HydraoClient\Api;

use Mips\HydraoClient\Model\AdviceModel;

class Advice extends AbstractApi {

    public function get(string $language = '') {
        $this->client->getLogger()->debug('GET advice');

        $data = array();
        if ($language==='en' || $language==='fr') {
            $data['local'] = $language;
        }
        return new AdviceResult($this->client->doGet('advice', $data));
    }
}

class AdviceResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = new AdviceModel($json);
    }

    public function getData(): AdviceModel {
        return $this->data;
    }

}