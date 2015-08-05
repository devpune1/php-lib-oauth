[![Build Status](https://travis-ci.org/fkooman/php-lib-oauth.svg)](https://travis-ci.org/fkooman/php-lib-oauth)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fkooman/php-lib-oauth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fkooman/php-lib-oauth/?branch=master)

# Introduction
Very simple OAuth 2.0 authorization server library. It is easy to create your 
own OAuth 2.0 server with this code and use any of the authentication methods
supported by `fkooman/rest-plugin-authentication`.

# API

    <?php

    ...

    $server = new OAuthServer(
        TemplateInterface $templateManager,
        ClientInterface $client,
        ResourceServerInterface $resourceServer,
        AuthorizationCodeInterface $authorizationCode, 
        AccessTokenInterface $accessToken
    );

    $service = new OAuthService($server);

    ...

