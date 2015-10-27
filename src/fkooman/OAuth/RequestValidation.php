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

use fkooman\Http\Request;
use fkooman\Http\Exception\BadRequestException;

class RequestValidation
{
    public static function validateAuthorizeRequest(Request $request, $requireState = true)
    {
        // REQUIRED client_id
        $clientId = $request->getUrl()->getQueryParameter('client_id');
        if (is_null($clientId)) {
            throw new BadRequestException('missing client_id');
        }
        if (false === InputValidation::clientId($clientId)) {
            throw new BadRequestException('invalid client_id');
        }

        // REQUIRED response_type
        $responseType = $request->getUrl()->getQueryParameter('response_type');
        if (is_null($responseType)) {
            throw new BadRequestException('missing response_type');
        }
        if (false === InputValidation::responseType($responseType)) {
            throw new BadRequestException('invalid response_type');
        }

        // REQUIRED redirect_uri
        $redirectUri = $request->getUrl()->getQueryParameter('redirect_uri');
        if (is_null($redirectUri)) {
            throw new BadRequestException('missing redirect_uri');
        }
        if (false === InputValidation::redirectUri($redirectUri)) {
            throw new BadRequestException('invalid redirect_uri');
        }

        // REQUIRED scope
        $scope = $request->getUrl()->getQueryParameter('scope');
        if (is_null($scope)) {
            throw new BadRequestException('missing scope');
        }
        if (false === InputValidation::scope($scope)) {
            throw new BadRequestException('invalid scope');
        }

        // REQUIRED state (but allow override with flag)
        $state = $request->getUrl()->getQueryParameter('state');
        if (is_null($state) && !$requireState) {
            $state = 'xxx_client_must_set_state_xxx';
        }
        if (is_null($state)) {
            throw new BadRequestException('missing state');
        }
        if (false === InputValidation::state($state)) {
            throw new BadRequestException('invalid state');
        }

        return array(
            'client_id' => $clientId,
            'response_type' => $responseType,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
        );
    }

    public static function validatePostAuthorizeRequest(Request $request)
    {
        $requestData = self::validateAuthorizeRequest($request);

        $approval = $request->getPostParameter('approval');
        if (is_null($approval)) {
            throw new BadRequestException('missing approval');
        }
        if (false === InputValidation::approval($approval)) {
            throw new BadRequestException('invalid approval');
        }

        $requestData['approval'] = $approval;

        return $requestData;
    }

    public static function validateDeleteApprovalRequest(Request $request)
    {
        // REQUIRED client_id
        $clientId = $request->getUrl()->getQueryParameter('client_id');
        if (is_null($clientId)) {
            throw new BadRequestException('missing client_id');
        }
        if (false === InputValidation::clientId($clientId)) {
            throw new BadRequestException('invalid client_id');
        }

        // REQUIRED response_type
        $responseType = $request->getUrl()->getQueryParameter('response_type');
        if (is_null($responseType)) {
            throw new BadRequestException('missing response_type');
        }
        if (false === InputValidation::responseType($responseType)) {
            throw new BadRequestException('invalid response_type');
        }

        // REQUIRED redirect_uri
        $redirectUri = $request->getUrl()->getQueryParameter('redirect_uri');
        if (is_null($redirectUri)) {
            throw new BadRequestException('missing redirect_uri');
        }
        if (false === InputValidation::redirectUri($redirectUri)) {
            throw new BadRequestException('invalid redirect_uri');
        }

        // REQUIRED scope
        $scope = $request->getUrl()->getQueryParameter('scope');
        if (is_null($scope)) {
            throw new BadRequestException('missing scope');
        }
        if (false === InputValidation::scope($scope)) {
            throw new BadRequestException('invalid scope');
        }

        return array(
            'client_id' => $clientId,
            'response_type' => $responseType,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
        );
    }

    public static function validateTokenRequest(Request $request)
    {
        // REQUIRED grant_type
        $grantType = $request->getPostParameter('grant_type');
        if (is_null($grantType)) {
            throw new BadRequestException('missing grant_type');
        }
        if (false === InputValidation::grantType($grantType)) {
            throw new BadRequestException('invalid grant_type');
        }

        // REQUIRED client_id
        $clientId = $request->getPostParameter('client_id');
        if (is_null($clientId)) {
            throw new BadRequestException('missing client_id');
        }
        if (false === InputValidation::clientId($clientId)) {
            throw new BadRequestException('invalid client_id');
        }

        // REQUIRED code
        $code = $request->getPostParameter('code');
        if (is_null($code)) {
            throw new BadRequestException('missing code');
        }
        if (false === InputValidation::code($code)) {
            throw new BadRequestException('invalid code');
        }

        // REQUIRED scope
        $scope = $request->getPostParameter('scope');
        if (is_null($scope)) {
            throw new BadRequestException('missing scope');
        }
        if (false === InputValidation::scope($scope)) {
            throw new BadRequestException('invalid scope');
        }

        // REQUIRED redirect_uri
        $redirectUri = $request->getPostParameter('redirect_uri');
        if (is_null($redirectUri)) {
            throw new BadRequestException('missing redirect_uri');
        }
        if (false === InputValidation::redirectUri($redirectUri)) {
            throw new BadRequestException('invalid redirect_uri');
        }

        return array(
            'grant_type' => $grantType,
            'client_id' => $clientId,
            'scope' => $scope,
            'code' => $code,
            'redirect_uri' => $redirectUri,
        );
    }

    public static function validateIntrospectRequest(Request $request)
    {
        // token
        $token = $request->getPostParameter('token');
        if (is_null($token)) {
            throw new BadRequestException('missing token');
        }
        if (false === InputValidation::token($token)) {
            throw new BadRequestException('invalid token');
        }

        return array(
            'token' => $token,
        );
    }
}
