<?php

namespace Mips\HydraoClient;

use InvalidArgumentException;
use RuntimeException;

class AccessToken {
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var int
     */
    protected $expires;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * Constructs an access token.
     *
     * @param array $options An array of options returned by the service provider
     *     in the access token request. The `access_token` option is required.
     * @throws InvalidArgumentException if `access_token` is not provided in `$options`.
     */
    public function __construct(array $options = []) {
        if (empty($options['access_token'])) {
            throw new InvalidArgumentException('Required option not passed: "access_token"');
        }

        $this->accessToken = $options['access_token'];

        if (!empty($options['refresh_token'])) {
            $this->refreshToken = $options['refresh_token'];
        }

        if (isset($options['expires_in'])) {
            if (!is_numeric($options['expires_in'])) {
                throw new \InvalidArgumentException('expires_in value must be an integer');
            }

            $this->expires = $options['expires_in'] != 0 ? time() + $options['expires_in'] : 0;
        }
    }

    public function getToken() {
        return $this->accessToken;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getExpires() {
        return $this->expires;
    }

    public function hasExpired() {
        $expires = $this->getExpires();

        if (empty($expires)) {
            throw new RuntimeException('"expires" is not set on the token');
        }

        return $expires < time();
    }

    public function __toString() {
        return (string)$this->getToken();
    }

    public function jsonSerialize() {
        if ($this->accessToken) {
            $parameters['access_token'] = $this->accessToken;
        }

        if ($this->refreshToken) {
            $parameters['refresh_token'] = $this->refreshToken;
        }

        if ($this->expires) {
            $parameters['expires'] = $this->expires;
        }

        if ($this->resourceOwnerId) {
            $parameters['resource_owner_id'] = $this->resourceOwnerId;
        }

        return $parameters;
    }
}
