<?php

namespace fkooman\OAuth;

interface AccessTokenInterface
{
    /**
     * Store an access token.
     *
     * @param AccessToken $accessToken the access token to store
     *
     * @return string the access token that will be provided to the
     *                client
     */
    public function store(AccessToken $accessToken);

    /**
     * Retrieve an access token.
     *
     * @param string $accessToken the access token received from
     *                            the resource server
     *
     * @return AccessToken|false the access token object if the
     *                           access token was found, or false if it was
     *                           not found
     */
    public function retrieve($accessToken);
}
