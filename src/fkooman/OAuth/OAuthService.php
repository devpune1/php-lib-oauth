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
use fkooman\Http\Response;
use fkooman\Rest\Plugin\Authentication\UserInfoInterface;
use fkooman\Tpl\TemplateManagerInterface;
use DateTime;

class OAuthService extends Service
{
    protected $templateManager;

    /** @var array */
    protected $options = array(
        'disable_token_endpoint' => false,
        'disable_introspect_endpoint' => false,
        'route_prefix' => '',
    );

    /** @var OAuthServer */
    protected $server;

    public function __construct(TemplateManagerInterface $templateManager, ClientStorageInterface $clientStorage, ResourceServerStorageInterface $resourceServerStorage, ApprovalStorageInterface $approvalStorage, AuthorizationCodeStorageInterface $authorizationCodeStorage, AccessTokenStorageInterface $accessTokenStorage, array $options = array(), DateTime $dateTime = null)
    {
        parent::__construct();

        $this->templateManager = $templateManager;
        $this->options = array_merge($this->options, $options);

        $this->server = new OAuthServer(
            $clientStorage,
            $resourceServerStorage,
            $approvalStorage,
            $authorizationCodeStorage,
            $accessTokenStorage,
            $this->options,
            $dateTime
        );
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $this->get(
            $this->options['route_prefix'].'/authorize',
            function (Request $request, UserInfoInterface $userInfo) {
                $authorize = $this->server->getAuthorize($request, $userInfo);
                if ($authorize instanceof Response) {
                    return $authorize;
                }
                // XXX here authorize must be array type!
                $response = new Response();
                $response->setHeader('X-Frame-Options', 'DENY');
                $response->setHeader('Content-Security-Policy', "default-src 'self'");
                $response->setBody(
                    $this->templateManager->render(
                        'getAuthorize',
                        $authorize
                    )
                );

                return $response;
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('user'),
                ),
            )
        );

        $this->post(
            $this->options['route_prefix'].'/authorize',
            function (Request $request, UserInfoInterface $userInfo) {
                return $this->server->postAuthorize($request, $userInfo);
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array(
                    'activate' => array('user'),
                ),
            )
        );

        if (!$this->options['disable_token_endpoint']) {
            $this->post(
                $this->options['route_prefix'].'/token',
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
        }

        if (!$this->options['disable_introspect_endpoint']) {
            $this->post(
                $this->options['route_prefix'].'/introspect',
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
}
