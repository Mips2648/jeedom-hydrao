<?php

namespace mips\hydraoapi\api;

use mips\hydraoapi\model\UserModel;

class Users extends AbstractApi {

    public function me() {
        $this->client->getLogger()->debug('GET users/me');
        return new UsersMeResult($this->client->executeRequest('GET', 'users/me'));
    }
}

class UsersMeResult extends AbstractResult {

    protected function loadData($json) {
        $this->data = new UserModel($json);
    }

    public function getData(): UserModel {
        return $this->data;
    }

}