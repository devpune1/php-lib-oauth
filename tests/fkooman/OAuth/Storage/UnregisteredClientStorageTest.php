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
}
