<?php

namespace Mips\HydraoClient\Model;

class UserStatsModel extends BaseModel {

    public function getAverageVolumeValue() {
        return $this->jsonData->average_volume->value;
    }

    public function getAverageDurationValue() {
        return $this->jsonData->average_duration->value;
    }

    public function getTotalEnergySavedValue() {
        return $this->jsonData->total_energy_saved->value;
    }

    public function getTotalVolumeSavedValue() {
        return $this->jsonData->total_volume_saved->value;
    }

    public function getTotalMoneySavedValue() {
        return $this->jsonData->total_money_saved->value;
    }

    public function getAverageVolumeTrend() {
        return $this->jsonData->average_volume->trend;
    }

    public function getAverageDurationTrend() {
        return $this->jsonData->average_duration->trend;
    }

    public function getTotalEnergySavedTrend() {
        return $this->jsonData->total_energy_saved->trend;
    }

    public function getTotalVolumeSavedTrend() {
        return $this->jsonData->total_volume_saved->trend;
    }

    public function getTotalMoneySavedTrend() {
        return $this->jsonData->total_money_saved->trend;
    }

    public function getChallengeLevel() {
        return $this->jsonData->challenge->level;
    }

    public function getChallengeScore() {
        return $this->jsonData->challenge->score;
    }
}