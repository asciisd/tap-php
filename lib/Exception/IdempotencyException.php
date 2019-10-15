<?php

namespace Tap\Exception;

/**
 * IdempotencyException is thrown in cases where an idempotency key was used
 * improperly.
 *
 * @package Tap\Exception
 */
class IdempotencyException extends ApiErrorException
{
}
