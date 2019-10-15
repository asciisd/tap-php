<?php

namespace Tap\Exception;

/**
 * InvalidRequestException is thrown when a request is initiated with invalid
 * parameters.
 *
 * @package Tap\Exception
 */
class InvalidRequestException extends ApiErrorException
{
    protected $tapParam;

    /**
     * Creates a new InvalidRequestException exception.
     *
     * @param string $message The exception message.
     * @param int|null $httpStatus The HTTP status code.
     * @param string|null $httpBody The HTTP body as a string.
     * @param array|null $jsonBody The JSON deserialized body.
     * @param array|\Tap\Util\CaseInsensitiveArray|null $httpHeaders The HTTP headers array.
     * @param string|null $tapCode The Tap error code.
     * @param string|null $tapParam The parameter related to the error.
     *
     * @return InvalidRequestException
     */
    public static function factory(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null,
        $tapCode = null,
        $tapParam = null
    ) {
        $instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $tapCode);
        $instance->setTapParam($tapParam);

        return $instance;
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
