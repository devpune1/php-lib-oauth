<?php

namespace fkooman\OAuth;

interface ResourceServerInterface
{
    /**
     * Retrieve a resource server based on resourceServerId.
     *
     * @param string $resourceServerId the resource server ID
     *
     * @return ResourceServerInfo|false if the resource server exists it
     *                                  returns ResourceServer, otherwise false
     */
    public function getResourceServer($resourceServerId);
}
