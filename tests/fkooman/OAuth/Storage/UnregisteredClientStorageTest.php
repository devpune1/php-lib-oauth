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

class UnregisteredClientStorageTest extends PHPUnit_Framework_TestCase
{
    public function testClient()
    {
        $clientStorage = new UnregisteredClientStorage();
        $client = $clientStorage->getClient('id', 'code', 'https://example.org/cb', 'foo bar');
        $this->assertSame('id', $client->getClientId());
        $this->assertSame('code', $client->getResponseType());
        $this->assertSame('https://example.org/cb', $client->getRedirectUri());
        $this->assertSame('foo bar', $client->getScope());
    }

    public function testMissingRedirectUriClient()
    {
        $clientStorage = new UnregisteredClientStorage();
        $this->assertFalse($clientStorage->getClient('id', 'code', null, 'foo bar'));
    }
}
