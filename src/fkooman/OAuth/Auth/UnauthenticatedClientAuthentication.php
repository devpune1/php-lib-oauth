<?php

/**
 * Copyright 2015 François Kooman <fkooman@tuxed.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace fkooman\OAuth\Auth;

use fkooman\Http\Request;
use fkooman\Rest\Service;
use fkooman\Rest\Plugin\Authentication\AuthenticationPluginInterface;

class UnauthenticatedClientAuthentication implements AuthenticationPluginInterface
{
    public function isAuthenticated(Request $request)
    {
        if (null === $clientId = $request->getPostParameter('client_id')) {
            return false;
        }

        return new UnauthenticatedClientUserInfo($clientId);
    }

    public function requestAuthentication(Request $request)
    {
        // NOP
    }

    public function init(Service $service)
    {
        // NOP
    }
}
