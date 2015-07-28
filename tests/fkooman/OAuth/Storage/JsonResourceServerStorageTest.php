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

class JsonResourceServerStorageTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $resourceServerStorage = new JsonResourceServerStorage(__DIR__.'/data/resource_servers.json');
        $resourceServer = $resourceServerStorage->getResourceServer('my_resource_server');
        $this->assertSame('my_resource_server', $resourceServer->getResourceServerId());
        $this->assertSame('$2y$10$cG3iFTTpitGAHYyci8bII.68.uRwvmSpCTvEfVmDwka5E2132XmAC', $resourceServer->getSecret());
        $this->assertSame('post', $resourceServer->getScope());
    }

    public function testGetNoScope()
    {
        $resourceServerStorage = new JsonResourceServerStorage(__DIR__.'/data/resource_servers.json');
        $resourceServer = $resourceServerStorage->getResourceServer('foo');
        $this->assertSame('foo', $resourceServer->getResourceServerId());
        $this->assertSame('$2y$10$pSkWUeL9cFX58JvYbNThW.40kMf5UF2JtBOTdFLg3Gttm9o21WKY.', $resourceServer->getSecret());
        $this->assertSame('read', $resourceServer->getScope());
    }

    public function testGetNonExisting()
    {
        $resourceServerStorage = new JsonResourceServerStorage(__DIR__.'/data/resource_servers.json');
        $this->assertFalse($resourceServerStorage->getResourceServer('non_existing'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage unable to read file
     */
    public function testMissingFile()
    {
        $resourceServerStorage = new JsonResourceServerStorage(__DIR__.'/data/missing.json');
        $resourceServerStorage->getResourceServer('foo');
    }
}
