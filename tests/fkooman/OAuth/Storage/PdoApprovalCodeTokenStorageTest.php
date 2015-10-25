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
namespace fkooman\OAuth\Storage;

use PHPUnit_Framework_TestCase;
use fkooman\OAuth\AuthorizationCode;
use fkooman\OAuth\AccessToken;
use fkooman\OAuth\Approval;
use PDO;

class PdoApprovalCodeTokenStorageTest extends PHPUnit_Framework_TestCase
{
    /** @var PdoAuthorizationCodeStorage */
    private $storage;

    public function setUp()
    {
        $io = $this->getMockBuilder('fkooman\IO\IO')->getMock();
        $io->expects($this->any())->method('getRandom')->will($this->returnValue('112233ff'));

        $this->storage = new PdoApprovalCodeTokenStorage(
            new PDO(
                $GLOBALS['DB_DSN'],
                $GLOBALS['DB_USER'],
                $GLOBALS['DB_PASSWD']
            ),
            '',
            $io
        );
        $this->storage->initDatabase();
    }

    public function testInsertCode()
    {
        $authorizationCode = new AuthorizationCode(
            'foo',
            'bar',
            123456789,
            'https://example.org/cb',
            'foo bar'
        );

        $this->assertSame('112233ff', $this->storage->storeAuthorizationCode($authorizationCode));
    }

    public function testGetCode()
    {
        $authorizationCode = new AuthorizationCode(
            'foo',
            'bar',
            123456789,
            'https://example.org/cb',
            'foo bar'
        );

        $this->assertSame('112233ff', $this->storage->storeAuthorizationCode($authorizationCode));
        $this->assertSame('foo', $this->storage->retrieveAuthorizationCode('112233ff')->getClientId());
    }

    public function testGetMissingCode()
    {
        $this->assertFalse($this->storage->retrieveAuthorizationCode('112233ff'));
    }

    public function testCodeLog()
    {
        $this->assertTrue($this->storage->isFreshAuthorizationCode('112233ff'));
        $this->assertFalse($this->storage->isFreshAuthorizationCode('112233ff'));
    }

    public function testInsertToken()
    {
        $accessToken = new AccessToken(
            'foo',
            'bar',
            123456789,
            'foo bar'
        );

        $this->assertSame('112233ff', $this->storage->storeAccessToken($accessToken));
    }

    public function testGetToken()
    {
        $accessToken = new AccessToken(
            'foo',
            'bar',
            123456789,
            'foo bar'
        );

        $this->assertSame('112233ff', $this->storage->storeAccessToken($accessToken));
        $this->assertSame('foo', $this->storage->retrieveAccessToken('112233ff')->getClientId());
    }

    public function testGetMissingToken()
    {
        $this->assertFalse($this->storage->retrieveAccessToken('112233ff'));
    }

    public function testStoreApproval()
    {
        $approval = new Approval(
            'user',
            'test-client',
            'https://example.org/cb',
            'code',
            'foo bar'
        );
        $this->assertTrue($this->storage->storeApproval($approval));
    }

    // this test throws an exception, should not store the same approval
    // twice!
#    public function testStoreApprovalApproval()
#    {
#        $approval = new Approval(
#            'foo',
#            'bar',
#            'foo bar'
#        );
#        $this->assertTrue($this->storage->storeApproval($approval));
#        $this->assertFalse($this->storage->storeApproval($approval));
#    }

    public function testIsApproved()
    {
        $approval = new Approval(
            'user',
            'test-client',
            'https://example.org/cb',
            'code',
            'foo bar'
        );
        $this->assertTrue($this->storage->storeApproval($approval));
        $this->assertTrue($this->storage->isApproved($approval));
    }

    public function testIsNotApproved()
    {
        $approval = new Approval(
            'user',
            'test-client',
            'https://example.org/cb',
            'code',
            'foo bar'
        );
        $this->assertFalse($this->storage->isApproved($approval));
    }

    public function testDeleteApproved()
    {
        $approval = new Approval(
            'user',
            'test-client',
            'https://example.org/cb',
            'code',
            'foo bar'
        );
        $this->assertTrue($this->storage->storeApproval($approval));
        $this->assertTrue($this->storage->deleteApproval($approval));
        $this->assertFalse($this->storage->deleteApproval($approval));
    }
}
