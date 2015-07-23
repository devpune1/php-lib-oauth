<?php

namespace fkooman\OAuth;

use fkooman\Rest\Service;
use fkooman\Http\Request;
use fkooman\Rest\Plugin\Authentication\UserInfoInterface;

class OAuthService extends Service
{
    /** @var OAuthServer */
    protected $server;

    public function __construct(OAuthServer $server)
    {
        parent::__construct();

        $this->server = $server;
        $this->registerRoutes();
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
                    //'activate' => array('client'),
                    'enabled' => false,
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
