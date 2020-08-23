<?php

namespace Mips\HydraoClient;

use Mips\Http\HttpClient;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * Hydrao Client
 * @author Mips
 */
class Client {
    /**
     * @var AccessToken
     */
    private $accesstoken;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(string $apiKey, LoggerInterface $logger = null) {
        $this->logger = $logger ?: new NullLogger();
        $this->httpClient = new HttpClient('https://api.hydrao.com', $this->logger);
        $this->httpClient->getHttpHeaders()->setHeader('x-api-key', $apiKey);
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

        $result = $this->httpClient->doPost('sessions', $data);
        if ($result->isSuccess()) {
            $this->accesstoken = new AccessToken(json_decode($result->getBody(), true));
            $this->httpClient->getHttpHeaders()->setHeader('Authorization', 'Bearer ' . $this->accesstoken->getToken());
            $this->logger->debug("access token header set");
            return true;
        }
        throw new RuntimeException("Login failed: ({$result->getHttpStatusCode()}){$result->getError()} - response received: {$result->getBody()}");
    }

    private $users;

    public function Users() {
        $this->logger->debug("users");
        return $this->users ?: ($this->users = new \Mips\HydraoClient\Api\Users($this->httpClient));
    }

    private $showerheads;

    public function ShowerHeads() {
        $this->logger->debug("ShowerHeads");
        return $this->showerheads ?: ($this->showerheads = new \Mips\HydraoClient\Api\ShowerHeads($this->httpClient));
    }

    private $advice;

    public function Advice() {
        $this->logger->debug("Advice");
        return $this->advice ?: ($this->advice = new \Mips\HydraoClient\Api\Advice($this->httpClient));
    }

    private $userStats;

    public function UserStats() {
        $this->logger->debug("UserStats");
        return $this->userStats ?: ($this->userStats = new \Mips\HydraoClient\Api\UserStats($this->httpClient));
    }
}
