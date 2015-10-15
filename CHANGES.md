# Release History

## 2.0.0 (2015-10-15)
- implement `token` response_type (RFC 6749#4.2)
- rename resource_servers table to resource_server (singular)
- relax `state` requirement in authorization requests
- allow for disabling `token` and `introspect` endpoints
- allow to specify an API "authentication" source to verify
  tokens locally

## 1.0.0 (2015-09-21)
- initial release
