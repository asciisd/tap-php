<?php

namespace Tap\Exception\OAuth;

/**
 * InvalidClientException is thrown when the client_id does not belong to you,
 * the tap_user_id does not exist or is not connected to your application,
 * or the API key mode (live or test mode) does not match the client_id mode.
 *
 * @package Tap\Exception\OAuth
 */
class InvalidClientException extends OAuthErrorException
{
}
