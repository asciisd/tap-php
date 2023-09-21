<?php

namespace Tap\Util;

/**
 * A very basic implementation of LoggerInterface that has just enough
 * functionality that it can be the default for this library.
 */
class DefaultLogger implements LoggerInterface
{
    public function error(string $message, array $context = []): void
    {
        if (count($context) > 0) {
            throw new \Tap\Exception\BadMethodCallException('DefaultLogger does not currently implement context. Please implement if you need it.');
        }
        error_log($message);
    }
}
