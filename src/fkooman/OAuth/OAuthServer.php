<?php

namespace fkooman\OAuth;

use fkooman\Rest\Plugin\Authentication\UserInfoInterface;
use fkooman\Http\Request;
use fkooman\Http\RedirectResponse;
use fkooman\Http\JsonResponse;
use fkooman\Http\Exception\BadRequestException;
use fkooman\IO\IO;

class OAuthServer
{
    /** @var TemplateInterface */
    private $templateManager;

    /** @var ClientInterface */
    private $client;

    /** @var ResourceServerInterface */
    private $resourceServer;

    /** @var AuthorizationCodeInterface */
    private $authorizationCode;

    /** @var AccessTokenInterface */
    private $accessToken;

    /** @var \fkooman\IO\IO */
    private $io;

    public function __construct(TemplateInterface $templateManager, ClientInterface $client, ResourceServerInterface $resourceServer, AuthorizationCodeInterface $authorizationCode, AccessTokenInterface $accessToken, IO $io = null)
    {
        $this->templateManager = $templateManager;
        $this->client = $client;
        $this->resourceServer = $resourceServer;
        $this->authorizationCode = $authorizationCode;
        $this->accessToken = $accessToken;
        if (null === $io) {
            $io = new IO();
        }
        $this->io = $io;
    }

    /**
     * Get the template manager.
     *
     * @return TemplateInterface the template manager instance
     */
    public function getTemplateManager()
    {
        return $this->templateManager;
    }

    public function getAuthorize(Request $request, UserInfoInterface $userInfo)
    {
        $authorizeRequest = RequestValidation::validateAuthorizeRequest($request);

        $clientInfo = $this->client->getClient(
            $authorizeRequest['client_id'],
            $authorizeRequest['response_type'],
            $authorizeRequest['redirect_uri'],
            $authorizeRequest['scope']
        );
        if (false === $clientInfo) {
            throw new BadRequestException('client does not exist');
        }

        // show the approval dialog
        return $this->templateManager->render(
            'getAuthorize',
            array(
                'user_id' => $userInfo->getUserId(),
                'client_id' => $clientInfo->getClientId(),
                'redirect_uri' => $clientInfo->getRedirectUri(),
                'scope' => $clientInfo->getScope(),
                'request_url' => $request->getUrl()->toString(),
            )
        );
    }

    public function postAuthorize(Request $request, UserInfoInterface $userInfo)
    {
        // FIXME: referrer url MUST be request URL?
        $postAuthorizeRequest = RequestValidation::validatePostAuthorizeRequest($request);

        if ('yes' === $postAuthorizeRequest['approval']) {
            // approved
            $code = $this->authorizationCode->storeAuthorizationCode(
                new AuthorizationCode(
                    $postAuthorizeRequest['client_id'],
                    $userInfo->getUserId(),
                    $this->io->getTime(),
                    $postAuthorizeRequest['redirect_uri'],
                    $postAuthorizeRequest['scope']
                )
            );

            $separator = false === strpos($postAuthorizeRequest['redirect_uri'], '?') ? '?' : '&';

            $redirectTo = sprintf(
                '%s%scode=%s&state=%s',
                $postAuthorizeRequest['redirect_uri'],
                $separator,
                $code,
                $postAuthorizeRequest['state']
            );

            return new RedirectResponse(
                $redirectTo,
                302
            );
        }

        // not approved
        $separator = false === strpos($postAuthorizeRequest['redirect_uri'], '?') ? '?' : '&';

        $redirectTo = sprintf(
            '%s%serror=access_denied&state=%s',
            $postAuthorizeRequest['redirect_uri'],
            $separator,
            $postAuthorizeRequest['state']
        );

        return new RedirectResponse(
            $redirectTo,
            302
        );
    }

    public function postToken(Request $request)
    {
        $tokenRequest = RequestValidation::validateTokenRequest($request);
        if (!$this->authorizationCode->isFreshAuthorizationCode($tokenRequest['code'])) {
            throw new BadRequestException('authorization code can not be replayed');
        }
        $authorizationCode = $this->authorizationCode->retrieveAuthorizationCode($tokenRequest['code']);

        $iat = $authorizationCode->getIssuedAt();
        if ($this->io->getTime() > $iat + 600) {
            throw new BadRequestException('authorization code expired');
        }

        if ($authorizationCode->getClientId() !== $tokenRequest['client_id']) {
            throw new BadRequestException('client_id does not match expected value');
        }
        if ($authorizationCode->getRedirectUri() !== $tokenRequest['redirect_uri']) {
            throw new BadRequestException('redirect_uri does not match expected value');
        }
        if ($authorizationCode->getScope() !== $tokenRequest['scope']) {
            throw new BadRequestException('scope does not match expected value');
        }

        // FIXME: grant_type must also match I think, but we do not have any
        // mapping logic from response_type to grant_type yet...

        // FIXME: keep log of used codes (must not allowed to be replayed)

        // create an access token
        $accessToken = $this->accessToken->storeAccessToken(
            new AccessToken(
                $authorizationCode->getClientId(),
                $authorizationCode->getUserId(),
                $this->io->getTime(),
                $authorizationCode->getRedirectUri(),
                $authorizationCode->getScope()
            )
        );

        $response = new JsonResponse();
        $response->setHeader('Cache-Control', 'no-store');
        $response->setHeader('Pragma', 'no-cache');
        $response->setBody(
            array(
                'access_token' => $accessToken,
                'scope' => $authorizationCode->getScope(),
            )
        );

        return $response;
    }

    public function postIntrospect(Request $request, UserInfoInterface $userInfo)
    {
        $introspectRequest = RequestValidation::validateIntrospectRequest($request);
        $accessToken = $this->accessToken->retrieveAccessToken($introspectRequest['token']);

        if (false === $accessToken) {
            $body = array(
                'active' => false,
            );
        } else {
            // FIXME: use better scope matching...
            $resourceServerInfo = $this->resourceServer->getResourceServer($userInfo->getUserId());
            if ($resourceServerInfo->getScope() !== $accessToken->getScope()) {
                $body = array(
                    'active' => false,
                );
            } else {
                $body = array(
                    'active' => true,
                    'client_id' => $accessToken->getClientId(),
                    'scope' => $accessToken->getScope(),
                    'token_type' => 'bearer',
                    'iat' => $accessToken->getIssuedAt(),
                    'sub' => $accessToken->getUserId(),
                );
            }
        }

        $response = new JsonResponse();
        $response->setBody($body);

        return $response;
    }
}
