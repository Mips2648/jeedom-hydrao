<?php

namespace Mips\HydraoClient\Api;

use Mips\HydraoClient\Model\UserModel;

class Users extends AbstractApi {

    public function me() {
        $this->client->getLogger()->debug('GET users/me');
        return new UsersMeResult($this->client->doGet('users/me'));
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