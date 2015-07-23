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
     *                                 authorization code was found, or false 
     *                                 if it was not found
     */
    public function retrieve($authorizationCode);

    /**
     * Check whether or not the authorizationCode was used before.
     *
     * @param string $authorizationCode the authorization code received from
     *                                  the client
     *
     * @return bool true if the code was not used before, false if it was used
     *              before. NOTE that as call to isFresh MUST mark that particular 
     *              authorization code as used IMMEDIATELY. 
     */
    public function isFresh($authorizationCode);
}
