<?php

namespace Mips\HydraoClient\Model;

class ShowerHeadShowerModel extends BaseModel {

    /**
     * Shower id
     */
    public function getId() {
        return $this->jsonData->shower_id;
    }

    /**
     * Shower volume in liter
     */
    public function getVolume() {
        return $this->jsonData->volume;
    }

    public function getTemperature() {
        return $this->jsonData->temperature;
    }

    /**
     * Average flow rate
     */
    public function getFlow() {
        return $this->jsonData->flow;
    }

    public function getSoapingTime() {
        return $this->jsonData->soaping_time;
    }

    public function getDuration() {
        return $this->jsonData->duration;
    }

    public function getDate() {
        return $this->jsonData->date;
    }

    public function isRealDate() {
        return $this->jsonData->is_real_date;
    }

    public function getNumberOfSoapings() {
        return $this->jsonData->number_of_soapings;
    }
}
