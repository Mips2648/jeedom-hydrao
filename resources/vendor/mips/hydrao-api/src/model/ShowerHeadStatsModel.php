<?php

namespace Mips\HydraoClient\Model;

class ShowerHeadStatsModel extends BaseModel {

    public function getVolumeAverage() {
        return $this->jsonData->volume_average;
    }

    public function getTrend() {
        return $this->jsonData->trend;
    }

    public function getNbItems() {
        return $this->jsonData->nb_items;
    }
}