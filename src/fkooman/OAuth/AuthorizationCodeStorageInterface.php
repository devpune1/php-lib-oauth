<?php

namespace fkooman\OAuth;

interface AuthorizationCodeStorageInterface
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
    public function storeAuthorizationCode(AuthorizationCode $authorizationCode);

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
    public function retrieveAuthorizationCode($authorizationCode);

    /**
     * Check whether or not the authorization code was used before.
     *
     * @param string $authorizationCode the authorization code received from
     *                                  the client
     *
     * @return bool true if the code was not used before, false if it was used
     *              before. NOTE: a call to isFresh MUST mark that particular 
     *              authorization code as used IMMEDIATELY. It must NEVER 
     *              respond with true for the same authorization code.
     */
    public function isFreshAuthorizationCode($authorizationCode);
}
