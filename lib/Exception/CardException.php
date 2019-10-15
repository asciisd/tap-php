<?php

namespace Tap\Exception;

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
     * @param string $message The exception message.
     * @param int|null $httpStatus The HTTP status code.
     * @param string|null $httpBody The HTTP body as a string.
     * @param array|null $jsonBody The JSON deserialized body.
     * @param array|\Tap\Util\CaseInsensitiveArray|null $httpHeaders The HTTP headers array.
     * @param string|null $tapCode The Tap error code.
     * @param string|null $declineCode The decline code.
     * @param string|null $tapParam The parameter related to the error.
     *
     * @return CardException
     */
    public static function factory(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null,
        $tapCode = null,
        $declineCode = null,
        $tapParam = null
    ) {
        $instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $tapCode);
        $instance->setDeclineCode($declineCode);
        $instance->setTapParam($tapParam);

        return $instance;
    }

    /**
     * Gets the decline code.
     *
     * @return string|null
     */
    public function getDeclineCode()
    {
        return $this->declineCode;
    }

    /**
     * Sets the decline code.
     *
     * @param string|null $declineCode
     */
    public function setDeclineCode($declineCode)
    {
        $this->declineCode = $declineCode;
    }

    /**
     * Gets the parameter related to the error.
     *
     * @return string|null
     */
    public function getTapParam()
    {
        return $this->tapParam;
    }

    /**
     * Sets the parameter related to the error.
     *
     * @param string|null $tapParam
     */
    public function setTapParam($tapParam)
    {
        $this->tapParam = $tapParam;
    }
}
