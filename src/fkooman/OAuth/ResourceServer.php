<?php

namespace fkooman\OAuth;

use InvalidArgumentException;

class ResourceServer
{
    /** @var string */
    private $resourceServerId;

    /** @var string */
    private $scope;

    /** @var string */
    private $secret;

    public function __construct($resourceServerId, $scope, $secret)
    {
        $this->resourceServerId = $resourceServerId;
        $this->scope = $scope;
        $this->secret = $secret;
    }

    public static function fromArray(array $resourceServer)
    {
        $requiredFields = array('resource_server_id', 'scope', 'secret');
        foreach ($requiredFields as $f) {
            if (!array_key_exists($f, $resourceServer)) {
                throw new InvalidArgumentException(
                    sprintf('missing "%s"', $f)
                );
            }
        }

        return new self(
            $resourceServer['resource_server_id'],
            $resourceServer['scope'],
            $resourceServer['secret']
        );
    }

    public function getResourceServerId()
    {
        return $this->resourceServerId;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
