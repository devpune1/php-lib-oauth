<?php

namespace fkooman\OAuth;

interface ClientInterface
{
    /**
     * Retrieve a client based on clientId, redirectUri and scope.
     *
     * @param string      $clientId     the client ID
     * @param string      $responseType the response type
     * @param string|null $redirectUri  the redirectUri
     * @param string|null $scope        the scope
     *
     * @return ClientInfo|false if the client exists with given parameters it
     *                          returns ClientInfo, otherwise false
     */
    public function getClient($clientId, $responseType, $redirectUri, $scope);
}
