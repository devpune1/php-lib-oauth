<?php

namespace fkooman\OAuth;

class ClientInfo
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $responseType;

    /** @var string */
    private $redirectUri;

    /** @var string */
    private $scope;

    public function __construct($clientId, $responseType, $redirectUri, $scope)
    {
        $this->clientId = $clientId;
        $this->responseType = $responseType;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
    }

    public static function fromArray(array $client)
    {
        return new self(
            $client['client_id'],
            $client['response_type'],
            $client['redirect_uri'],
            $client['scope']
        );
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
}
