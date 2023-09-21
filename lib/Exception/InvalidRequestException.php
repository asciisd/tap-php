<?php

namespace Tap\Exception;

use Tap\Util\CaseInsensitiveArray;

/**
 * InvalidRequestException is thrown when a request is initiated with invalid
 * parameters.
 *
 * @package Tap\Exception
 */
class InvalidRequestException extends ApiErrorException
{
    protected ?string $tapParam = null;

    /**
     * Creates a new InvalidRequestException exception.
     *
     * @param  string  $message  The exception message.
     * @param  ?int  $httpStatus  The HTTP status code.
     * @param  ?string  $httpBody  The HTTP body as a string.
     * @param  ?array  $jsonBody  The JSON deserialized body.
     * @param  array|CaseInsensitiveArray|null  $httpHeaders  The HTTP headers array.
     * @param  ?string  $tapCode  The Tap error code.
     *
     * @return InvalidRequestException
     */
    public static function factory(
        string $message,
        int $httpStatus = null,
        string $httpBody = null,
        array $jsonBody = null,
        array|CaseInsensitiveArray $httpHeaders = null,
        string $tapCode = null
    ) {
        return parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $tapCode);
    }

    /**
     * Gets the parameter related to the error.
     */
    public function getTapParam(): ?string
    {
        return $this->tapParam;
    }

    /**
     * Sets the parameter related to the error.
     */
    public function setTapParam(?string $tapParam): void
    {
        $this->tapParam = $tapParam;
    }
}
