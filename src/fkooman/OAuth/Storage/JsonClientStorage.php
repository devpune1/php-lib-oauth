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

    public function getClient($clientId, $responseType = null, $redirectUri = null, $scope = null)
    {
        $data = Json::decodeFile($this->jsonFile);
        if (!array_key_exists($clientId, $data)) {
            return false;
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
