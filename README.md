[![Build Status](https://travis-ci.org/fkooman/php-lib-oauth.svg)](https://travis-ci.org/fkooman/php-lib-oauth)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fkooman/php-lib-oauth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fkooman/php-lib-oauth/?branch=master)

# Introduction
Create an OAuth 2.0 Authorization Server (AS). 

# How to Use
To use this library you need to do some things:

1. figure out how to store client, resource server, token, code and approval
   data. Typically you store those in a database, but they could also be JSON
   files or whatever is appropriate for your environment;
2. figure out the authentication. There are various endpoints with various 
   authentication requirements.

# Deployment Scenarios
This library supports two different deployment scenarios:

1. built in in the REST service itself
2. as a stand alone OAuth 2.0 authorization server controlling access to 
   multiple clients and resource servers

# Storage
We have three things that require (dynamic) storage:

1. approval of client by user with a certain scope
2. authorization codes (only for authorization code profile)
3. access tokens

And two things that require (more or less) static storage:

1. client data
2. resource server data

For the dynamic storage it makes most sense to use the included 
`PdoApprovalCodeTokenStorage` class. For storing clients and resource servers
there are also some options available, like JSON, or the option to allow 
unregistered clients.

    $db = new PDO(...);
    $pdoApprovalCodeTokenStorage = new PdoAppovalCodeTokenStorage($db);

    $clientStorage = new JsonClientStorage('/path/to/clients.json');
    $resourceServerStorage = new JsonResourceServerStorage('/path/to/resource_servers.json');

The format for the JSON files is a hash where the key is respectivly the 
`client_id` or `resource_server_id`. For example:

    {
        "my_client": {
            "scope": "read",
            "secret": "$2y$10$cG3iFTTpitGAHYyci8bII.68.uRwvmSpCTvEfVmDwka5E2132XmAC",
            "redirect_uri": "https://example.org/cb",
            "response_type": "code"
        }
    }

See the `Client` and `ResourceServer` classes for the supported fields.

# Authentication
For just the OAuth 2.0 part there are possibly four authentication mechanisms 
you have to consider:

1. authenticating the user (using the web browser)
2. authenticating clients (allow them to obtain access tokens)
3. authenticating resource servers (only in stand alone scenario)
4. authenticating clients accessing the protected resource (`Bearer` token, 
   not in the stand alone scenario)

Several helper classes are available to make this easy, but it needs to be 
configured for your REST service or OAuth server deployment.

Using `fkooman/rest-plugin-authentication` these plugins can be registered, 
they need the following friendly names, in the same order as before:

1. `user`
2. `client`
3. `resource_server`
4. `api`

A typical scenario where this library is used as part of the REST service is 
the following:

For `user` authentication one would use 
`fkooman/rest-plugin-authentication-form`, for `client` authentication 
`fkooman/rest-plugin-authentication-basic` and for access to the protected 
resources `api` with `fkooman/rest-plugin-authentication-bearer`. All these
mechanisms need to be configured.

## User
One could just have a static list with user/hash combinations, for example
in a configuration file or a database. Here it is just statically configured
in the code itself:

    $userAuth = new FormAuthentication(
        function($userName) {
            if('foo' === $userName) {
                return '<hash value of password>';
            }

            return false;
        },
        array('realm' => 'OAuth 2.0 User Authentication')
    );

## Client
The client authentication scenario is similar to the one for the user in that
clients also authentication with username and password, or in OAuth terminology
with `client_id` and `client_secret`. The interesting thing here is that it 
make sense to have this use the client storage from above available in 
`$clientStorage` without needing to duplicate storing secrets somewhere else.

    $clientAuth = new BasicAuthentication(
        function ($clientId) use ($clientStorage) {
            $client = $clientStorage->getClient($clientId);
            if (false === $client) {
                return false;
            }

            return $client->getSecret();
        },
        array('realm' => 'OAuth 2.0 Client Authentication')
    );

## API
The `BearerAuthentication` class is special in that it doesn't return a simple
hash, but it returns a object with more information about the token in an 
object of type `TokenInfo`. It is able to also provide the `client_id`, the 
`scope` and for example the `username` of the user granting the permission to
the client. This can be used by the API to have more fine grained control 
whether or not a certain call is allowed, for example based on `scope`. 

**TELL SOMETHING ABOUT DbTokenValidator**

    $apiAuth = new BearerAuthentication(
        ...
    );

Now these mechanisms must all be fed to the `AuthenticationPlugin` object:

    $authenticationPlugin = new AuthenticationPlugin();
    $authenticationPlugin->register($userAuth, 'user');
    $authenticationPlugin->register($clientAuth, 'client');
    $authenticationPlugin->register($apiAuth, 'api');
    //$authenticationPlugin->register($resourceServerAuth, 'resource_server');

That's all for the authentication!

# Service
Instantiating the service is quite easy now:

    // allow for disabling token and/or introspection endpoint, defaults shown
    // here
    $serviceOptions = array(
        'disable_token_endpoint' => false,
        'disable_introspect_endpoint' => false,    
        'oauth_route_prefix' => '',   // e.g.: '/_oauth'
    );
 
    // here $approvalStorage, $authorizationCodeStorage and $accessTokenStorage
    // can just be $pdoApprovalCodeTokenStorage!
   
    $service = new OAuthService(
        $clientStorage,
        $resourceServerStorage,
        $approvalStorage,
        $authorizationCodeStorage,
        $accessTokenStorage,
        $serviceOptions
    )

    // register the authentication plugin with all its methods
    $service->registerDefaultPlugin($authenticationPlugin);
     
    // the protected resource!
    $service->get(
        '/protected_resource',
        function(TokenInfo $tokenInfo) {
            return json_encode(
                array(
                    'username' => $tokenInfo->getSub()
                )
            );
        },
        array(
            'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                'activate' => array('api'),
            )
        )
    );

    // run the service and send the response
    $service->run()->send();

That is all!
