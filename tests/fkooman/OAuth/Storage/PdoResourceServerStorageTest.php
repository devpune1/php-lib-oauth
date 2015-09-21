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
use PDO;

class PdoResourceServerStorageTest extends PHPUnit_Framework_TestCase
{
    /** @var PdoResourceServerStorage */
    private $storage;

    public function setUp()
    {
        $db = new PDO(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD']
        );

        $this->storage = new PdoResourceServerStorage($db);
        $this->storage->initDatabase();

        // add a resource server
        $db->query(
            'INSERT INTO resource_servers (id, scope, secret) VALUES("foo", "read", "$2y$10$vrHBaF01p9yqbOksTrR7aueltwHS4WA.dCktSHlrjDcFub.rKZuSa")'
        );
    }

    public function testGetResourceServer()
    {
        $resourceServer = $this->storage->getResourceServer('foo');
        $this->assertSame('foo', $resourceServer->getResourceServerId());
        $this->assertSame('read', $resourceServer->getScope());
        $this->assertSame('$2y$10$vrHBaF01p9yqbOksTrR7aueltwHS4WA.dCktSHlrjDcFub.rKZuSa', $resourceServer->getSecret());
    }

    public function testNonExistingResourceServer()
    {
        $this->assertFalse($this->storage->getResourceServer('bar'));
    }
}
