<?php

namespace fkooman\OAuth;

use fkooman\Rest\Service;
use fkooman\Http\Request;
use fkooman\Rest\Plugin\Authentication\Basic\BasicAuthentication;
use fkooman\Rest\Plugin\Authentication\AuthenticationPlugin;
use fkooman\Rest\Plugin\Authentication\UserInfoInterface;
use fkooman\Rest\Plugin\Authentication\AuthenticationPluginInterface;

class OAuthService extends Service
{
    /** @var OAuthServer */
    protected $server;

    public function __construct(OAuthServer $server, AuthenticationPluginInterface $userAuthenticationPlugin)
    {
        parent::__construct();

        $this->server = $server;
        $this->registerAuthenticationPlugin($userAuthenticationPlugin);
        $this->registerRoutes();
    }

    private function registerAuthenticationPlugin(AuthenticationPluginInterface $userAuthenticationPlugin)
    {
        $authenticationPlugin = new AuthenticationPlugin();

        // register 'user' authentication
        $authenticationPlugin->register($userAuthenticationPlugin, 'user');

        // register 'client' authentication
        $clientAuthentication = new BasicAuthentication(
            function ($clientId) {
                $client = $this->server->getClientStorage()->getClient($clientId);
                if (false === $client) {
                    return false;
                }

                return $client->getSecret();
            },
            array(
                'realm' => 'OAuth AS',
            )
        );
        $authenticationPlugin->register($clientAuthentication, 'client');

        // register 'resource server' authentication
        $resourceServerAuthentication = new BasicAuthentication(
            function ($resourceServerId) {
                $resourceServer = $this->server->getResourceServerStorage()->getResourceServer($resourceServerId);
                if (false === $resourceServer) {
                    return false;
                }

                return $resourceServer->getSecret();
            },
            array(
                'realm' => 'OAuth AS',
            )
        );
        $authenticationPlugin->register($resourceServerAuthentication, 'resource_server');

        $this->getPluginRegistry()->registerDefaultPlugin($authenticationPlugin);
    }

    private function registerRoutes()
    {
        $this->get(
            '/authorize',
            function (Request $request, UserInfoInterface $userInfo) {
                return $this->server->getAuthorize($request, $userInfo);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('user'),
                ),
            )
        );

        $this->post(
            '/authorize',
            function (Request $request, UserInfoInterface $userInfo) {
                return $this->server->postAuthorize($request, $userInfo);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('user'),
                ),
            )

        );

        $this->post(
            '/token',
            function (Request $request) {
                return $this->server->postToken($request);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('client')
                ),
            )
        );

        $this->post(
            '/introspect',
            function (Request $request, UserInfoInterface $userInfo) {
                return $this->server->postIntrospect($request, $userInfo);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('resource_server'),
                ),
            )
        );
    }
}
