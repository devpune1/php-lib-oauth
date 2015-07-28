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

use fkooman\OAuth\AuthorizationCodeStorageInterface;
use fkooman\OAuth\AuthorizationCode;
use fkooman\Json\Json;
use fkooman\Base64\Base64Url;

class TestAuthorizationCode implements AuthorizationCodeStorageInterface
{
    public function storeAuthorizationCode(AuthorizationCode $authorizationCode)
    {
        return Base64Url::encode(
            Json::encode(
                array(
                    'client_id' => $authorizationCode->getClientId(),
                    'user_id' => $authorizationCode->getUserId(),
                    'issued_at' => $authorizationCode->getIssuedAt(),
                    'redirect_uri' => $authorizationCode->getRedirectUri(),
                    'scope' => $authorizationCode->getScope(),
                )
            )
        );
    }

    public function retrieveAuthorizationCode($authorizationCode)
    {
        return AuthorizationCode::fromArray(
            Json::decode(
                Base64Url::decode($authorizationCode)
            )
        );
    }

    public function isFreshAuthorizationCode($authorizationCode)
    {
        if ('replayed_code' === $authorizationCode) {
            return false;
        }

        return true;
    }
}
