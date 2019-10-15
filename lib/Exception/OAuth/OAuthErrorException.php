<?php

namespace Tap\Exception\OAuth;

/**
 * Implements properties and methods common to all (non-SPL) Tap OAuth
 * exceptions.
 */
abstract class OAuthErrorException extends \Tap\Exception\ApiErrorException
{
    protected function constructErrorObject()
    {
        if (is_null($this->jsonBody)) {
            return null;
        }

        return \Tap\OAuthErrorObject::constructFrom($this->jsonBody);
    }
}
