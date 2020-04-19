<?php

namespace mips\hydraoapi\model;

class ShowerModel {

    private $jsonData;

    public function __construct($json) {
        $this->jsonData = $json;
    }

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
        return $this->jsonData->temerature;
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