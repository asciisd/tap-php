<?php

namespace Tap\Exception\OAuth;

/**
 * InvalidRequestException is thrown when a code, refresh token, or grant
 * type parameter is not provided, but was required.
 *
 * @package Tap\Exception\OAuth
 */
class InvalidRequestException extends OAuthErrorException
{
}
