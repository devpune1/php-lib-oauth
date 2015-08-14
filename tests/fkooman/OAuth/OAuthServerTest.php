<?php

/**
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace fkooman\OAuth;

require_once __DIR__.'/Test/TestTemplateManager.php';
require_once __DIR__.'/Test/TestAuthorizationCode.php';
require_once __DIR__.'/Test/TestAccessToken.php';
require_once __DIR__.'/Test/TestClient.php';

use PHPUnit_Framework_TestCase;
use fkooman\OAuth\Test\TestTemplateManager;
use fkooman\OAuth\Test\TestAuthorizationCode;
use fkooman\OAuth\Test\TestAccessToken;
use fkooman\OAuth\Test\TestClient;
use fkooman\Http\Request;

class OAuthServerTest extends PHPUnit_Framework_TestCase
{
    /** @var \fkooman\OAuth\OAuthServer */
    private $oauthServer;

    /** @var \fkooman\Rest\Plugin\Authentication\UserInfoInterface */
    private $userInfo;

    /** @var \fkooman\Rest\Plugin\Authentication\UserInfoInterface */
    private $clientUserInfo;

    public function setUp()
    {
        $this->userInfo = $this->getMockBuilder('fkooman\Rest\Plugin\Authentication\UserInfoInterface')->getMock();
        $this->userInfo->method('getUserId')->willReturn('admin');

        $this->clientUserInfo = $this->getMockBuilder('fkooman\Rest\Plugin\Authentication\UserInfoInterface')->getMock();
        $this->clientUserInfo->method('getUserId')->willReturn('test-client');

        $testTemplateManager = new TestTemplateManager();
        $testAuthorizationCode = new TestAuthorizationCode();
        $testAccessToken = new TestAccessToken();

        $resourceServer = $this->getMockBuilder('fkooman\OAuth\ResourceServerStorageInterface')->getMock();
        $resourceServer->method('getResourceServer')->willReturn(
            new ResourceServer(
                'r_id',
                'post',
                'SECRET'
            )
        );

        $io = $this->getMockBuilder('fkooman\IO\IO')->getMock();
        $io->method('getTime')->willReturn(1234567890);

        $this->oauthServer = new OAuthServer(
            $testTemplateManager,
            new TestClient(),
            $resourceServer,
            $testAuthorizationCode,
            $testAccessToken,
            $io
        );
    }

    public function testGetAuthorize()
    {
        $query = array(
            'client_id' => 'test-client',
            'response_type' => 'code',
            'redirect_uri' => 'https://localhost/cb',
            'state' => '12345',
            'scope' => 'post',
        );
        $request = $this->getAuthorizeRequest($query, 'GET');

        $this->assertSame(
            array(
                'getAuthorize' => array(
                    'user_id' => 'admin',
                    'client_id' => 'test-client',
                    'redirect_uri' => 'https://localhost/cb',
                    'scope' => 'post',
                    'request_url' => 'https://oauth.example/authorize?client_id=test-client&response_type=code&redirect_uri=https%3A%2F%2Flocalhost%2Fcb&state=12345&scope=post',
                ),
            ),
            $this->oauthServer->getAuthorize($request, $this->userInfo)
        );
    }

    public function testPostAuthorize()
    {
        $query = array(
            'client_id' => 'test-client',
            'redirect_uri' => 'https://localhost/cb',
            'state' => '12345',
            'response_type' => 'code',
            'scope' => 'post',
        );
        $request = $this->getAuthorizeRequest($query, 'POST', array('approval' => 'yes'));

        $this->assertSame(
            array(
                'HTTP/1.1 302 Found',
                'Content-Type: text/html;charset=UTF-8',
                'Location: https://localhost/cb?code=eyJjbGllbnRfaWQiOiJ0ZXN0LWNsaWVudCIsInVzZXJfaWQiOiJhZG1pbiIsImlzc3VlZF9hdCI6MTIzNDU2Nzg5MCwicmVkaXJlY3RfdXJpIjoiaHR0cHM6XC9cL2xvY2FsaG9zdFwvY2IiLCJzY29wZSI6InBvc3QifQ&state=12345',
                '',
                '',
            ),
            $this->oauthServer->postAuthorize($request, $this->userInfo)->toArray()
        );
    }

    public function testPostAuthorizeNoApproval()
    {
        $query = array(
            'client_id' => 'test-client',
            'redirect_uri' => 'https://localhost/cb',
            'state' => '12345',
            'response_type' => 'code',
            'scope' => 'post',
        );
        $request = $this->getAuthorizeRequest($query, 'POST', array('approval' => 'no'));

        $this->assertSame(
            array(
                'HTTP/1.1 302 Found',
                'Content-Type: text/html;charset=UTF-8',
                'Location: https://localhost/cb?error=access_denied&state=12345',
                '',
                '',
            ),
            $this->oauthServer->postAuthorize($request, $this->userInfo)->toArray()
        );
    }

    public function testPostToken()
    {
        $request = new Request(
            array(
                'HTTPS' => 'on',
                'SERVER_NAME' => 'oauth.example',
                'SERVER_PORT' => '443',
                'REQUEST_URI' => '/token',
                'SCRIPT_NAME' => '/index.php',
                'PATH_INFO' => '/token',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'POST',
            ),
            array(
                'code' => 'eyJjbGllbnRfaWQiOiJ0ZXN0LWNsaWVudCIsInVzZXJfaWQiOiJhZG1pbiIsImlzc3VlZF9hdCI6MTIzNDU2Nzg5MCwicmVkaXJlY3RfdXJpIjoiaHR0cHM6XC9cL2xvY2FsaG9zdFwvY2IiLCJzY29wZSI6InBvc3QifQ',
                'scope' => 'post',
                'redirect_uri' => 'https://localhost/cb',
                'grant_type' => 'authorization_code',
                'client_id' => 'test-client',
            )
        );

        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                'Cache-Control: no-store',
                'Pragma: no-cache',
                '',
                '{"access_token":"eyJjbGllbnRfaWQiOiJ0ZXN0LWNsaWVudCIsInVzZXJfaWQiOiJhZG1pbiIsImlzc3VlZF9hdCI6MTIzNDU2Nzg5MCwic2NvcGUiOiJwb3N0In0","scope":"post"}',
            ),
            $this->oauthServer->postToken($request, $this->clientUserInfo)->toArray()
        );
    }

    /**
     * @expectedException fkooman\Http\Exception\UnauthorizedException
     * @expectedExceptionMessage not_authenticated
     */
    public function testPostTokenNoAuthentication()
    {
        // this should only work with clients that have 'secret' set to NULL
        $request = new Request(
            array(
                'HTTPS' => 'on',
                'SERVER_NAME' => 'oauth.example',
                'SERVER_PORT' => '443',
                'REQUEST_URI' => '/token',
                'SCRIPT_NAME' => '/index.php',
                'PATH_INFO' => '/token',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'POST',
            ),
            array(
                'code' => 'eyJjbGllbnRfaWQiOiJ0ZXN0LWNsaWVudCIsInVzZXJfaWQiOiJhZG1pbiIsImlzc3VlZF9hdCI6MTIzNDU2Nzg5MCwicmVkaXJlY3RfdXJpIjoiaHR0cHM6XC9cL2xvY2FsaG9zdFwvY2IiLCJzY29wZSI6InBvc3QifQ',
                'scope' => 'post',
                'redirect_uri' => 'https://localhost/cb',
                'grant_type' => 'authorization_code',
                'client_id' => 'test-client',
            )
        );
        $this->oauthServer->postToken($request, null);
    }

    /**
     * @expectedException fkooman\Http\Exception\BadRequestException
     * @expectedExceptionMessage authorization code can not be replayed
     */
    public function testPostTokenReplay()
    {
        $request = new Request(
            array(
                'HTTPS' => 'on',
                'SERVER_NAME' => 'oauth.example',
                'SERVER_PORT' => '443',
                'REQUEST_URI' => '/token',
                'SCRIPT_NAME' => '/index.php',
                'PATH_INFO' => '/token',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'POST',
            ),
            array(
                'code' => 'replayed_code',
                'scope' => 'post',
                'redirect_uri' => 'https://localhost/cb',
                'grant_type' => 'authorization_code',
                'client_id' => 'test-client',
            )
        );
        $this->oauthServer->postToken($request, $this->clientUserInfo);
    }

    public function testPostIntrospect()
    {
        $request = new Request(
            array(
                'HTTPS' => 'on',
                'SERVER_NAME' => 'oauth.example',
                'SERVER_PORT' => '443',
                'REQUEST_URI' => '/introspect',
                'SCRIPT_NAME' => '/index.php',
                'PATH_INFO' => '/introspect',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'POST',
            ),
            array(
                'token' => 'eyJjbGllbnRfaWQiOiJ0ZXN0LWNsaWVudCIsInVzZXJfaWQiOiJhZG1pbiIsImlzc3VlZF9hdCI6MTIzNDU2Nzg5MCwic2NvcGUiOiJwb3N0In0',
            )
        );

        $this->assertSame(
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: application/json',
                '',
                '{"active":true,"client_id":"test-client","scope":"post","token_type":"bearer","iat":1234567890,"sub":"admin"}',
            ),
            $this->oauthServer->postIntrospect($request, $this->userInfo)->toArray()
        );
    }

    private function getAuthorizeRequest(array $query, $requestMethod = 'GET', $postBody = array())
    {
        $q = http_build_query($query);

        return new Request(
            array(
                'HTTPS' => 'on',
                'SERVER_NAME' => 'oauth.example',
                'SERVER_PORT' => '443',
                'REQUEST_URI' => sprintf('/authorize?%s', $q),
                'SCRIPT_NAME' => '/index.php',
                'PATH_INFO' => '/authorize',
                'QUERY_STRING' => $q,
                'REQUEST_METHOD' => $requestMethod,
            ),
            $postBody
        );
    }
}
