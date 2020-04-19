<?php

namespace mips\hydraoapi;

use Mips\Http\HttpClient;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Hydrao Client
 * @author Mips
 */
class Client extends HttpClient {
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
            $this->accesstoken = new AccessToken(json_decode($result->getBody(), true));
            $this->headers[] = 'Authorization: Bearer ' . $this->accesstoken->getToken();
            $this->getLogger()->debug("access token saved");
            return true;
        }
        throw new RuntimeException("Login failed: ({$result->getHttpStatusCode()}){$result->getError()} - response received: {$result->getBody()}");
    }

    public function getToken() {
        return $this->accesstoken;
    }

    private $users;

    public function Users() {
        $this->getLogger()->debug("users");
        return $this->users ?: ($this->users = new \hydraoapi\api\Users($this));
    }

    private $showerheads;

    public function ShowerHeads() {
        $this->getLogger()->debug("ShowerHeads");
        return $this->showerheads ?: ($this->showerheads = new \hydraoapi\api\ShowerHeads($this));
    }

    /**
     * Hydrao showers info
     * @return ShowersResult
     */
    public function getShowers($deviceUUID, int $limit=null, int $fromId=null): ShowersResult
    {
        $data = array();
        if (!is_null($limit) && $limit>0) {
            $data['limit'] = $limit;
        }
        if (!is_null($fromId) && $fromId>0) {
            $data['fromid'] = $fromId;
        }

        $params = Client::buildQueryString($data);
        $this->logger->debug("params builded for getShowersInfo: {$params}");

        return new ShowersResult($this->executeRequest('GET', "shower-heads/{$deviceUUID}/showers?{$params}"));
    }

    private $advice;

    public function Advice() {
        $this->getLogger()->debug("Advice");
        return $this->advice ?: ($this->advice = new \hydraoapi\api\Advice($this));
    }

    /**
     * Hydrao user-stats
     * @return UserStatsResult
     */
    public function getUserStats(int $nbShowers=null): UserStatsResult
    {
        $data = array();
        if (!is_null($nbShowers) && $nbShowers>0) {
            $data['nbShowers'] = $nbShowers;
        }
        $params = Client::buildQueryString($data);
        $this->logger->debug("params builded for getUserStats: {$params}");

        return new UserStatsResult($this->executeRequest('GET', "user-stats?{$params}"));
    }

    /**
     * Hydrao shower-head stats
     * @return ShowerHeadStatsResult
     */
    public function getShowerHeadStats($deviceUUID, int $nbShowers=null): ShowerHeadStatsResult
    {
        $data = array();
        if (!is_null($nbShowers) && $nbShowers>0) {
            $data['nbShowers'] = $nbShowers;
        }
        $params = Client::buildQueryString($data);
        $this->logger->debug("params builded for getUserStats: {$params}");

        return new ShowerHeadStatsResult($this->executeRequest('GET', "shower-heads/{$deviceUUID}/stats"));
    }

    protected static function buildQueryString(array $params)
    {
        return http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
    }

}
