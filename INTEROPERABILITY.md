# Interoperability
This library is a bit stricter than the OAuth 2.0 RFC prescribes. This document
lists all changes you need to consider when using a client with software that
uses this library.

The rationale for this: less exceptions need to be handled and dealt with 
which makes the code simpler and easier to test.

This document is WIP.

# Authorization Requests
For authorization requests the parameters `redirect_uri`, `scope` and `state`
MUST be provided. 

## References

- https://tools.ietf.org/html/rfc6749#section-4.1.1
- https://tools.ietf.org/html/rfc6749#section-4.2.1

# Token Requests
For the token request, all parameters MUST be provided, So `redirect_uri` MUST
always be provided. Also `client_id` MUST always be provided, even if 
the client is authenticated.

## References

- https://tools.ietf.org/html/rfc6749#section-4.1.3
