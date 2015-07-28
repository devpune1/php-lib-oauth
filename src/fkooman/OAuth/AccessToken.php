<?php

namespace fkooman\OAuth;

class AccessToken
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $userId;

    /** @var int */
    private $issuedAt;

    /** @var string */
    private $scope;

    public function __construct($clientId, $userId, $issuedAt, $scope)
    {
        $this->clientId = $clientId;
        $this->userId = $userId;
        $this->issuedAt = $issuedAt;
        $this->scope = $scope;
    }

    public static function fromArray(array $accessToken)
    {
        return new self(
            $accessToken['client_id'],
            $accessToken['user_id'],
            $accessToken['issued_at'],
            $accessToken['scope']
        );
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
