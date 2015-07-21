<?php

namespace fkooman\OAuth;

interface AuthorizationCodeInterface
{
    /**
     * Store an authorization code.
     *
     * @param AuthorizationCode $authorizationCode the authorization code to
     *                                             store
     *
     * @return string the authorization code that will be provided to the
     *                client
     */
    public function store(AuthorizationCode $authorizationCode);

    /**
     * Retrieve an authorization code.
     *
     * @param string $authorizationCode the authorization code received from
     *                                  the client
     *
     * @return AuthorizationCode|false the authorization code object if the
     *                                 authorization code was found, or false if it was not found
     */
    public function retrieve($authorizationCode);
}
