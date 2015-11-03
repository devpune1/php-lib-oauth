# Release History

## 3.0.1 (2015-11-03)
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
