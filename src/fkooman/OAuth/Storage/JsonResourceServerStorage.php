<?php

namespace fkooman\OAuth\Storage;

use fkooman\OAuth\ResourceServerStorageInterface;
use fkooman\OAuth\ResourceServer;
use fkooman\Json\Json;

class JsonResourceServerStorage implements ResourceServerStorageInterface
{
    /** @var string */
    private $jsonFile;

    public function __construct($jsonFile)
    {
        $this->jsonFile = $jsonFile;
    }

    public function getResourceServer($resourceServerId)
    {
        $data = Json::decodeFile($this->jsonFile);
        if (!array_key_exists($resourceServerId, $data)) {
            return false;
        }
        $resourceServerData = $data[$resourceServerId];
        $resourceServerData['resource_server_id'] = $resourceServerId;

        return ResourceServer::fromArray($resourceServerData);
    }
}
