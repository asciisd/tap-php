<?php

namespace Tap\Exception;

/**
 * ApiConnection is thrown in the event that the SDK can't connect to Tap's
 * servers. That can be for a variety of different reasons from a downed
 * network to a bad TLS certificate.
 *
 * @package Tap\Exception
 */
class ApiConnectionException extends ApiErrorException
{
}
