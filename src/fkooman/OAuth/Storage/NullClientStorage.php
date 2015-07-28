<?php

namespace fkooman\OAuth\Storage;

use fkooman\OAuth\ClientStorageInterface;
use fkooman\OAuth\Client;

class NullClientStorage implements ClientStorageInterface
{
    public function getClient($clientId, $responseType, $redirectUri, $scope)
    {
        // only when there is actual client registration the redirectUri and
        // scope are optional as they can be retrieve from the registration
        // data, because there is no registration we require them to be set
        // explicitly
        if (null === $redirectUri || null === $scope) {
            return false;
        }

        return new Client($clientId, $responseType, $redirectUri, $scope, null);
    }
}
