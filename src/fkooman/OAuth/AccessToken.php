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

namespace fkooman\OAuth;

class AccessToken
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $userId;

    /** @var int */
    private $issuedAt;

    /** @var string */
    private $scope;

    public function __construct($clientId, $userId, $issuedAt, $scope)
    {
        $this->clientId = $clientId;
        $this->userId = $userId;
        $this->issuedAt = $issuedAt;
        $this->scope = $scope;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
