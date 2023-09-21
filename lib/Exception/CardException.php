<?php

namespace Tap\Exception;

use Tap\Util\CaseInsensitiveArray;

/**
 * CardException is thrown when a user enters a card that can't be charged for
 * some reason.
 *
 * @package Tap\Exception
 */
class CardException extends ApiErrorException
{
    protected $declineCode;
    protected $tapParam;

    /**
     * Creates a new CardException exception.
     *
     * @param  string  $message The exception message.
     * @param  ?int  $httpStatus The HTTP status code.
     * @param  ?string  $httpBody The HTTP body as a string.
     * @param  ?array  $jsonBody The JSON deserialized body.
     * @param  array|CaseInsensitiveArray|null  $httpHeaders The HTTP headers array.
     * @param  ?string  $tapCode The Tap error code.
     *
     * @return CardException
     */
    public static function factory(
        string $message,
        int $httpStatus = null,
        string $httpBody = null,
        array $jsonBody = null,
        array|CaseInsensitiveArray $httpHeaders = null,
        string $tapCode = null
    ) {
        $instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $tapCode);
        $instance->setDeclineCode($declineCode);
        $instance->setTapParam($tapParam);

        return $instance;
    }

    /**
     * Gets the decline code.
     *
     * @return ?string
     */
    public function getDeclineCode()
    {
        return $this->declineCode;
    }

    /**
     * Sets the decline code.
     *
     * @param ?string $declineCode
     */
    public function setDeclineCode($declineCode)
    {
        $this->declineCode = $declineCode;
    }

    /**
     * Gets the parameter related to the error.
     *
     * @return ?string
     */
    public function getTapParam()
    {
        return $this->tapParam;
    }

    /**
     * Sets the parameter related to the error.
     *
     * @param ?string $tapParam
     */
    public function setTapParam($tapParam)
    {
        $this->tapParam = $tapParam;
    }
}
