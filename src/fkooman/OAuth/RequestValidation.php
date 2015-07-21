<?php

namespace fkooman\OAuth;

use fkooman\Http\Request;
use fkooman\Http\Exception\BadRequestException;

class RequestValidation
{
    public static function validateAuthorizeRequest(Request $request)
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

        // OPTIONAL redirect_uri
        $redirectUri = $request->getUrl()->getQueryParameter('redirect_uri');
        if (false === InputValidation::redirectUri($redirectUri)) {
            throw new BadRequestException('invalid redirect_uri');
        }

        // OPTIONAL scope
        $scope = $request->getUrl()->getQueryParameter('scope');
        if (null !== $scope) {
            if (false === InputValidation::scope($scope)) {
                throw new BadRequestException('invalid scope');
            }
        }

        // RECOMMENDED, but treat as REQUIRED state
        $state = $request->getUrl()->getQueryParameter('state');
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

        // REQUIRED|OPTIONAL scope
        $scope = $request->getPostParameter('scope');
        if (false === InputValidation::scope($scope)) {
            throw new BadRequestException('invalid scope');
        }

        // REQUIRED|OPTIONAL redirect_uri
        $redirectUri = $request->getPostParameter('redirect_uri');
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
        if (false === InputValidation::token($token)) {
            throw new BadRequestException('invalid token');
        }

        return array(
            'token' => $token,
        );
    }
}
