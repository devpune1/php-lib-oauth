<?php

namespace fkooman\OAuth;

class Client
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $responseType;

    /** @var string */
    private $redirectUri;

    /** @var string */
    private $scope;

    /** @var string */
    private $secret;

    public function __construct($clientId, $responseType, $redirectUri, $scope, $secret)
    {
        $this->clientId = $clientId;
        $this->responseType = $responseType;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
        $this->secret = $secret;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getResponseType()
    {
        return $this->responseType;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
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
