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

class JsonClientStorageTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $clientStorage = new JsonClientStorage(__DIR__.'/data/clients.json');
        $client = $clientStorage->getClient('my_client', 'code');
        $this->assertSame('my_client', $client->getClientId());
        $this->assertSame('$2y$10$cG3iFTTpitGAHYyci8bII.68.uRwvmSpCTvEfVmDwka5E2132XmAC', $client->getSecret());
        $this->assertSame('read', $client->getScope());
        $this->assertSame('https://example.org/cb', $client->getRedirectUri());
        $this->assertSame('code', $client->getResponseType());
    }

    public function testGetNonExisting()
    {
        $clientStorage = new JsonClientStorage(__DIR__.'/data/clients.json');
        $this->assertFalse($clientStorage->getClient('non_existing', 'code'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage unable to read file
     */
    public function testMissingFile()
    {
        $clientStorage = new JsonClientStorage(__DIR__.'/data/missing.json');
        $clientStorage->getClient('foo', 'code');
    }
}
