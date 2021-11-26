<?php

namespace Mips\HydraoClient\Model;

class UserModel extends BaseModel {

    public function getId() {
        return $this->jsonData->id;
    }

    public function getEmail() {
        return $this->jsonData->email;
    }

    public function getCountryCode() {
        return $this->jsonData->country_code;
    }

    public function getCurrencyCode() {
        return $this->jsonData->currency_code;
    }

    public function getWaterUnit() {
        return $this->jsonData->water_unit;
    }

    public function getEnergyPrice() {
        return $this->jsonData->energy_price;
    }

    public function getHeatingEnergy() {
        return $this->jsonData->heating_energy;
    }

    public function getMinShowerLiter() {
        return $this->jsonData->min_shower_liter;
    }

    public function getStatsNbShowers() {
        return $this->jsonData->stats_nb_showers;
    }
}
