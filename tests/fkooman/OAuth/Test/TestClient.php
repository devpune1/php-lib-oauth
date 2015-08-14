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
namespace fkooman\OAuth\Test;

use fkooman\OAuth\ClientStorageInterface;
use fkooman\OAuth\Client;

class TestClient implements ClientStorageInterface
{
    public function getClient($clientId, $responseType = null, $redirectUri = null, $scope = null)
    {
        // XXX do something if the redirect_uri and scope are not matching...
        if ('test-client' === $clientId) {
            return new Client(
                $clientId,
                'code',
                'https://localhost/cb',
                'post',
                '$2y$10$l.ebSWe5xsSBKaaUqisVFetaIiGfjU.tnYjjL/izt95Rr5LNSYH4q'
            );
        }

        // XXX do something if the redirect_uri and scope are not matching...
        if ('test-anonymous-client' === $clientId && 'code' === $responseType) {
            return new Client(
                $clientId,
                'code',
                'https://localhost/cb',
                'post',
                null   // no secret set
            );
        }

        // not registered
        return false;
    }
}
