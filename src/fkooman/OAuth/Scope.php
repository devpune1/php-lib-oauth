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

use InvalidArgumentException;

class Scope
{
    /** @var array */
    private $scope;

    public function __construct($scope = null)
    {
        if (null === $scope) {
            $this->scope = array();
        } else {
            if (!is_string($scope)) {
                throw new InvalidArgumentException('argument must be string');
            }
            if (0 === strlen($scope)) {
                $this->scope = array();
            } else {
                $scopeTokens = explode(' ', $scope);
                foreach ($scopeTokens as $token) {
                    $this->validateScopeToken($token);
                }
                sort($scopeTokens, SORT_STRING);
                $this->scope = array_values(array_unique($scopeTokens, SORT_STRING));
            }
        }
    }

    private function validateScopeToken($scopeToken)
    {
        if (!is_string($scopeToken) || 0 >= strlen($scopeToken)) {
            throw new InvalidArgumentException('scope token must be a non-empty string');
        }
        if (1 !== preg_match('/^(?:\x21|[\x23-\x5B]|[\x5D-\x7E])+$/', $scopeToken)) {
            throw new InvalidArgumentException('invalid characters in scope token');
        }
    }

    public function hasScopeToken($scopeToken)
    {
        $this->validateScopeToken($scopeToken);

        return in_array($scopeToken, $this->scope);
    }

    /**
     * Check if all scope tokens from the provided scope are in this object's
     * scope tokens.
     *
     * @param Scope $scope the scope object to check
     *
     * @return bool
     */
    public function hasScope(Scope $scope)
    {
        foreach ($scope->toArray() as $scopeToken) {
            if (!$this->hasScopeToken($scopeToken)) {
                return false;
            }
        }

        return true;
    }

    public function toArray()
    {
        return $this->scope;
    }

    public function toString()
    {
        return implode(' ', $this->scope);
    }

    public function __toString()
    {
        return $this->toString();
    }
}
