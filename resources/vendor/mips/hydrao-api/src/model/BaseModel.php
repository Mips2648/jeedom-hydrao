<?php

namespace Mips\HydraoClient\Model;

class BaseModel {

    protected $jsonData;

    public function __construct($json) {
        $this->jsonData = $json;
    }

    public function __toString()
    {
        return json_encode($this->jsonData);
    }
}