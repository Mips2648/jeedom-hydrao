<?php

namespace Mips\HydraoClient\Model;

class AdviceModel extends BaseModel {

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
