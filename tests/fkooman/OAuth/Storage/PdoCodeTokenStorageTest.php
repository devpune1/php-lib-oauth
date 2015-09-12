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
namespace fkooman\OAuth\Storage;

use PHPUnit_Framework_TestCase;
use fkooman\OAuth\AuthorizationCode;
use fkooman\OAuth\AccessToken;
use PDO;

class PdoCodeTokenStorageTest extends PHPUnit_Framework_TestCase
{
    /** @var PdoAuthorizationCodeStorage */
    private $storage;

    public function setUp()
    {
        $io = $this->getMockBuilder('fkooman\IO\IO')->getMock();
        $io->expects($this->any())->method('getRandom')->will($this->returnValue('112233ff'));

        $this->storage = new PdoCodeTokenStorage(
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
}
