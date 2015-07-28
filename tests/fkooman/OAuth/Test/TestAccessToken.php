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

use fkooman\OAuth\AccessTokenStorageInterface;
use fkooman\OAuth\AccessToken;
use fkooman\Json\Json;
use fkooman\Base64\Base64Url;

class TestAccessToken implements AccessTokenStorageInterface
{
    public function storeAccessToken(AccessToken $accessToken)
    {
        return Base64Url::encode(
            Json::encode(
                array(
                    'client_id' => $accessToken->getClientId(),
                    'user_id' => $accessToken->getUserId(),
                    'issued_at' => $accessToken->getIssuedAt(),
                    'scope' => $accessToken->getScope(),
                )
            )
        );
    }

    public function retrieveAccessToken($accessToken)
    {
        return AccessToken::fromArray(
            Json::decode(
                Base64Url::decode($accessToken)
            )
        );
    }
}
