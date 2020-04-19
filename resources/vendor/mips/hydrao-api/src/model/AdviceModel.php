<?php

namespace mips\hydraoapi\model;

class AdviceModel {

    private $jsonData;

    public function __construct($json) {
        $this->jsonData = $json;
    }

    public function getAdviceId() {
        return $this->jsonData->advice_id;
    }

    public function getTitle() {
        return $this->jsonData->title;
    }

    public function getDescription() {
        return $this->jsonData->description;
    }
}