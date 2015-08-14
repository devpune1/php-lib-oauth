<?php

namespace fkooman\OAuth;

interface ClientStorageInterface
{
    /**
     * Retrieve a client based on clientId, responseType, redirectUri and 
     * scope. The parameters except the clientId are optional and are used to
     * support non-registered clients. 
     *
     * @param string      $clientId     the clientId
     * @param string|null $responseType the responseType
     * @param string|null $redirectUri  the redirectUri
     * @param string|null $scope        the scope
     *
     * @return Client|false if the client exists with the clientId it returns
     *                      Client, otherwise false
     */
    public function getClient($clientId, $responseType = null, $redirectUri = null, $scope = null);
}
