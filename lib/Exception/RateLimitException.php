<?php

namespace Tap\Exception;

/**
 * RateLimitException is thrown in cases where an account is putting too much
 * load on Tap's API servers (usually by performing too many requests).
 * Please back off on request rate.
 *
 * @package Tap\Exception
 */
class RateLimitException extends InvalidRequestException
{
}
