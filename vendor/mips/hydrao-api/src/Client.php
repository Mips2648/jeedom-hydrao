<?php

namespace Mips\HydraoClient;

use Mips\Http\HttpClient;
use Mips\HydraoClient\Api\Advice;
use Mips\HydraoClient\Api\ShowerHeads;
use Mips\HydraoClient\Api\Users;
use Mips\HydraoClient\Api\UserStats;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use InvalidArgumentException;

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
     * Create a new session from user and password
     * Access Token will be set in header and returned
     * @param string $user
     * @param string $pass
     * @throws Exception
     */
    public function newSession(string $user, string $pass) {
        if ($user == '' || $pass == '') {
            throw new InvalidArgumentException("You must provide user and password.");
        }
        $data = [
            'email' => $user,
            'password' => $pass,
        ];
        return $this->doLogin($data);
    }

    public function openSession(AccessToken $token) {
        if ($token->hasExpired()) {
            $data = [
                'refreshToken"' => $token->getRefreshToken()
            ];
            return $this->doLogin($data);
        } else {
            return $this->setToken($token);
        }
    }

    private function doLogin($data) {
        $result = $this->httpClient->doPost('sessions', $data);
        if ($result->isSuccess()) {
            return $this->setToken(new AccessToken(json_decode($result->getBody(), true)));
        } else {
            throw new RuntimeException("Login failed: ({$result->getHttpStatusCode()}){$result->getError()} - response received: {$result->getBody()}");
        }
    }

    private function setToken(AccessToken $token) {
        $this->accesstoken = $token;
        $this->httpClient->getHttpHeaders()->setHeader('Authorization', 'Bearer ' . $this->accesstoken->getToken());
        $this->logger->debug("access token header set");
        return $this->accesstoken;
    }

    private $users;

    public function users() {
        $this->logger->debug("users");
        return $this->users ?: ($this->users = new Users($this->httpClient));
    }

    /**
     * @var ShowerHeads
     */
    private $showerheads;

    public function showerHeads() {
        return $this->showerheads ?: ($this->showerheads = new ShowerHeads($this->httpClient));
    }

    /**
     * @var Advice
     */
    private $advice;

    public function advice() {
        return $this->advice ?: ($this->advice = new Advice($this->httpClient));
    }

    /**
     * @var UserStats
     */
    private $userStats;

    public function userStats() {
        return $this->userStats ?: ($this->userStats = new UserStats($this->httpClient));
    }
}
