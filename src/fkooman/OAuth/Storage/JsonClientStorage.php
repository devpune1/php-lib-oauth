<?php

namespace fkooman\OAuth\Storage;

use fkooman\OAuth\ClientStorageInterface;
use fkooman\OAuth\Client;
use fkooman\Json\Json;

class JsonClientStorage implements ClientStorageInterface
{
    /** @var string */
    private $jsonFile;

    public function __construct($jsonFile)
    {
        $this->jsonFile = $jsonFile;
    }

    public function getClient($clientId, $responseType, $redirectUri = null, $scope = null)
    {
        $data = Json::decodeFile($this->jsonFile);
        if (!array_key_exists($clientId, $data)) {
            return false;
        }
        if ($responseType !== $data[$clientId]['response_type']) {
            return false;
        }

        if (null !== $redirectUri) {
            if ($redirectUri !== $data[$clientId]['redirect_uri']) {
                return false;
            }
        }

        if (null !== $scope) {
            // XXX: add Scope check, or remove scope completely and rely on
            // resource server configuration exclusively to see which scopes
            // are allowed?
        }

        return new Client(
            $clientId,
            $data[$clientId]['response_type'],
            $data[$clientId]['redirect_uri'],
            $data[$clientId]['scope'],
            $data[$clientId]['secret']
        );
    }
}
