<?php

namespace fkooman\OAuth;

interface ResourceServerInterface
{
    /**
     * Retrieve a client based on clientId, redirectUri and scope.
     *
     * @param string      $resourceServerId the resource server ID
     * @param string|null $scope            the scope
     *
     * @return ResourceServerInfo|false if the resource server exists with given
     *                                  parameters it returns ResourceServer, otherwise false
     */
    public function getResourceServer($resourceServerId);
}
