# Release History

## 5.2.1 (2016-08-10)
- do not require `secret` to be specified in the client config, assume
  `null` when not specified

## 5.2.0 (2016-08-10)
- add `ArrayClientStorage`

## 5.1.2 (2016-08-07)
- do no longer require the path to be part of the `redirect_uri` as that is 
  not required by the specification

## 5.1.1 (2016-05-25)
- update `fkooman/io` dependency to new major version

## 5.1.0 (2016-03-30)
- add `NullApprovalStorage` if you never want to store approvals
- add `OAuthModule` now next to `OAuthService` which is being deleted in 
  next major release
- add `NoResourceServerStorage` when there are no registered resource 
  servers
- add `UnauthenticatedClientAuthentication` to support unauthenticated
  "authorization code grant" clients using the token endpoint
- add `NullAuthorizationCodeStorage` when using only implicit grant
  profile, no need to store authorization codes

## 5.0.3 (2016-03-25)
- update `fkooman/io` which no longer uses openssl for random data

## 5.0.2 (2016-03-25)
- update `fkooman/json`
- fix API incompatibility with TestTemplateManager
- source formatting

## 5.0.1 (2015-11-24)
- add `X-Frame-Options` and `Content-Security-Policy` response headers to 
  authorize dialog

## 5.0.0 (2015-11-19)
- update authentication dependencies

## 4.0.1 (2015-11-18)
- use `client_id` from `Client` object instead of from request URI as the 
  `client_id` can be rewritten in unregistered client situation, for example
  with remoteStorage clients (issue #2)

## 4.0.0 (2015-11-03)
- remove `UnregisteredClientStorage` class, dangerous in the wrong hands
- remove `redirect_uri` from `Approval` class, no need to have that
 
## 3.0.0 (2015-10-27)
- add support for Approvals to remember applications the user has granted 
  permissions to
- rename `access_tokens`, `authorization_codes` and `authorization_codes_log` 
  tables to singular
- remove `PdoCodeTokenStorage` and use three different classes (API change)
- major API overhaul
- much better documentation
- big cleanup and simplification
- additional unit test coverage
- be more strict in required parameters, but allow for override to disable
  required `state`

## 2.0.0 (2015-10-15)
- implement `token` response_type (RFC 6749#4.2)
- rename resource_servers table to resource_server (singular)
- relax `state` requirement in authorization requests
- allow for disabling `token` and `introspect` endpoints
- allow to specify an API "authentication" source to verify
  tokens locally

## 1.0.0 (2015-09-21)
- initial release
