<?php

// Tap singleton
require(dirname(__FILE__) . '/lib/Tap.php');

// Utilities
require(dirname(__FILE__) . '/lib/Util/CaseInsensitiveArray.php');
require(dirname(__FILE__) . '/lib/Util/LoggerInterface.php');
require(dirname(__FILE__) . '/lib/Util/DefaultLogger.php');
require(dirname(__FILE__) . '/lib/Util/RandomGenerator.php');
require(dirname(__FILE__) . '/lib/Util/RequestOptions.php');
require(dirname(__FILE__) . '/lib/Util/Set.php');
require(dirname(__FILE__) . '/lib/Util/Util.php');

// HttpClient
require(dirname(__FILE__) . '/lib/HttpClient/ClientInterface.php');
require(dirname(__FILE__) . '/lib/HttpClient/CurlClient.php');

// Exceptions
require(dirname(__FILE__) . '/lib/Exception/ExceptionInterface.php');
require(dirname(__FILE__) . '/lib/Exception/ApiErrorException.php');
require(dirname(__FILE__) . '/lib/Exception/ApiConnectionException.php');
require(dirname(__FILE__) . '/lib/Exception/AuthenticationException.php');
require(dirname(__FILE__) . '/lib/Exception/BadMethodCallException.php');
require(dirname(__FILE__) . '/lib/Exception/CardException.php');
require(dirname(__FILE__) . '/lib/Exception/IdempotencyException.php');
require(dirname(__FILE__) . '/lib/Exception/InvalidArgumentException.php');
require(dirname(__FILE__) . '/lib/Exception/InvalidRequestException.php');
require(dirname(__FILE__) . '/lib/Exception/PermissionException.php');
require(dirname(__FILE__) . '/lib/Exception/RateLimitException.php');
require(dirname(__FILE__) . '/lib/Exception/SignatureVerificationException.php');
require(dirname(__FILE__) . '/lib/Exception/UnexpectedValueException.php');
require(dirname(__FILE__) . '/lib/Exception/UnknownApiErrorException.php');

// OAuth exceptions
require(dirname(__FILE__) . '/lib/Exception/OAuth/ExceptionInterface.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/OAuthErrorException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/InvalidClientException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/InvalidGrantException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/InvalidRequestException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/InvalidScopeException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/UnknownOAuthErrorException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/UnsupportedGrantTypeException.php');
require(dirname(__FILE__) . '/lib/Exception/OAuth/UnsupportedResponseTypeException.php');

// API operations
require(dirname(__FILE__) . '/lib/ApiOperations/All.php');
require(dirname(__FILE__) . '/lib/ApiOperations/Create.php');
require(dirname(__FILE__) . '/lib/ApiOperations/Delete.php');
require(dirname(__FILE__) . '/lib/ApiOperations/NestedResource.php');
require(dirname(__FILE__) . '/lib/ApiOperations/Request.php');
require(dirname(__FILE__) . '/lib/ApiOperations/Retrieve.php');
require(dirname(__FILE__) . '/lib/ApiOperations/Update.php');

// Plumbing
require(dirname(__FILE__) . '/lib/ApiResponse.php');
require(dirname(__FILE__) . '/lib/RequestTelemetry.php');
require(dirname(__FILE__) . '/lib/TapObject.php');
require(dirname(__FILE__) . '/lib/ApiRequestor.php');
require(dirname(__FILE__) . '/lib/ApiResource.php');
require(dirname(__FILE__) . '/lib/SingletonApiResource.php');

// Tap API Resources
require(dirname(__FILE__) . '/lib/Collection.php');
require(dirname(__FILE__) . '/lib/Customer.php');
require(dirname(__FILE__) . '/lib/Authorize.php');
require(dirname(__FILE__) . '/lib/Card.php');
require(dirname(__FILE__) . '/lib/Charge.php');
require(dirname(__FILE__) . '/lib/Token.php');
