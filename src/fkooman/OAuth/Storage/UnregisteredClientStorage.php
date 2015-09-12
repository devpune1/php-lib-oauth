<?php

namespace fkooman\OAuth\Storage;

use fkooman\OAuth\ClientStorageInterface;
use fkooman\OAuth\Client;

class UnregisteredClientStorage implements ClientStorageInterface
{
    public function getClient($clientId, $responseType = null, $redirectUri = null, $scope = null)
    {
        // only when there is actual client registration the redirectUri and
        // scope are optional as they can be retrieve from the registration
        // data, because there is no registration we require them to be set
        // explicitly
        if (null === $responseType || null === $redirectUri || null === $scope) {
            return false;
        }

        return new Client($clientId, $responseType, $redirectUri, $scope, null);
    }
}