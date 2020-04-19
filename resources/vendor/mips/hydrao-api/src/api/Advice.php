<?php

namespace mips\hydraoapi\api;

use mips\hydraoapi\model\AdviceModel;

class Advice extends AbstractApi {

    public function index($language = '') {
        $this->client->getLogger()->debug('GET advice');


        $data = array();
        if ($language==='en' || $language==='fr') {
            $data['local'] = $language;
        }
        return new AdviceResult($this->client->executeRequest('GET', 'advice', $data));
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