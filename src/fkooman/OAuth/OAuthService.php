<?php

/**
 *  Copyright 2015 François Kooman <fkooman@tuxed.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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

    public function __construct(OAuthServer $server, AuthenticationPluginInterface $userAuth, AuthenticationPluginInterface $apiAuth)
    {
        parent::__construct();

        $this->server = $server;
        $this->registerAuthenticationPlugin($userAuth, $apiAuth);
        $this->registerRoutes();
    }

    private function registerAuthenticationPlugin(AuthenticationPluginInterface $userAuth, AuthenticationPluginInterface $apiAuth)
    {
        $authenticationPlugin = new AuthenticationPlugin();

        // register 'user' authentication
        $authenticationPlugin->register($userAuth, 'user');

        // register 'api' authentication
        $authenticationPlugin->register($apiAuth, 'api');

        // register 'client' authentication
        $clientAuth = new BasicAuthentication(
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
        $authenticationPlugin->register($clientAuth, 'client');

        // register 'resource server' authentication
        $resourceServerAuth = new BasicAuthentication(
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
        $authenticationPlugin->register($resourceServerAuth, 'resource_server');

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
            function (Request $request, UserInfoInterface $userInfo = null) {
                return $this->server->postToken($request, $userInfo);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('client'),
                    'require' => false,
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
