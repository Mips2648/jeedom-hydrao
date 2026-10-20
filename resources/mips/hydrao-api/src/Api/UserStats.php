<?php

namespace Mips\HydraoClient\Api;

use Mips\HydraoClient\Model\UserStatsModel;

class UserStats extends AbstractApi {

    public function get($nbShowers = null) {
        $this->client->getLogger()->debug('GET UserStats');

        $data = array();
        if (is_numeric($nbShowers) && $nbShowers > 0) {
            $data['nbShowers'] = $nbShowers;
        }
        return new UserStatsResult($this->client->doGet('user-stats', $data));
    }
}

class UserStatsResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = new UserStatsModel($json);
    }

    public function getData(): UserStatsModel {
        return $this->data;
    }
}
