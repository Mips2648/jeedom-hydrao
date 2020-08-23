<?php

namespace Mips\HydraoClient\Model;

class ShowerHeadModel extends BaseModel {

    public function getDeviceUUID() {
        return $this->jsonData->device_uuid;
    }

    public function getLabel() {
        return $this->jsonData->label;
    }

    public function getType() {
        return $this->jsonData->type;
    }

    public function getMACAddress() {
        return $this->jsonData->mac_address;
    }

    public function getLastSyncDate() {
        return $this->jsonData->last_sync_date;
    }

    public function getHWVersion() {
        return $this->jsonData->hw_version;
    }

    public function getFWVersion() {
        return $this->jsonData->fw_version;
    }

    // {
    //     "device_uuid":"0c47xxxxxxxx0045",
    //     "first_seen":"2019-08-08T20:47:41.000Z",
    //     "last_seen":"2020-03-26T07:29:56.000Z",
    //     "baseline_start":null,
    //     "baseline_stop":null,
    //     "hw_version":"x",
    //     "fw_version":"2xxxxxx8",
    //     "threshold":"[{\"color\":\"FF00FF00\",\"liter\":15},{\"color.............}]",
    //     "upgrade_date":null,
    //     "upgrade_failed":null,
    //     "upgrade_from_version":null,
    //     "gateway_uuid":"73axxxxxxxxxxxxb420",
    //     "label":"Douche",
    //     "previous_flow":12,
    //     "type":"aloe",
    //     "mac_address":"2F0xxxxxxxxxxxxxx89C",
    //     "last_sync_min_index":338,
    //     "last_sync_max_index":577,
    //     "last_sync_date":"2020-03-26T07:30:02.000Z",
    //     "ref_shower_duration":null,
    //     "calibration":545,
    //     "is_last_sync_complete":true,
    //     "serial":null,
    //     "place_id":null,
    //     "connectivity":"BLE",
    //     "is_threshold_change_pending":false
    // }
}