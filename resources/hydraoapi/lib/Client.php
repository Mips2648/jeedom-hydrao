<?php

namespace hydraoapi;

use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Hydrao Client
 * @author Mips
 */
class Client extends BaseClient {
    private $accesstoken;

    public function __construct(string $apiKey, LoggerInterface $logger = null) {
        parent::__construct('https://api.hydrao.com', $logger);
        $this->headers[] = 'x-api-key: '.$apiKey;
    }

    /**
     * login
     * @param string $user
     * @param string $pass
     * @throws Exception
     */
    public function login(string $user, string $pass) {
        $data = [
            'email' => $user,
            'password' => $pass,
        ];

        $result = $this->executeRequest('POST', 'sessions', $data);
        if ($result->isSuccess()) {
            $this->accesstoken = new AccessToken($result->getResponse());
            $this->headers[] = 'Authorization: Bearer ' . $this->accesstoken->getToken();
            return true;
        }
        throw new RuntimeException("Login failed: ({$result->getHttpStatusCode()}){$result->getError()} - response received: {$result->getResponse()}");
    }

    public function getToken() {
        return $this->accesstoken;
    }

    /**
     * Hydrao User data API
     * @return Result
     */
    public function getUserInfo(): Result
    {
        return $this->executeRequest('GET', 'users/me');
    }

}
