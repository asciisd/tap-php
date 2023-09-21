<?php

namespace Tap\Exception;

use Exception;
use Tap\ErrorObject;
use Tap\Util\CaseInsensitiveArray;

/**
 * Implements properties and methods common to all (non-SPL) Tap exceptions.
 */
abstract class ApiErrorException extends Exception implements ExceptionInterface
{
    protected $error;
    protected $httpBody;
    protected $httpHeaders;
    protected $httpStatus;
    protected $jsonBody;
    protected $requestId;
    protected $tapCode;

    /**
     * Creates a new API error exception.
     *
     * @param  string  $message The exception message.
     * @param  ?int  $httpStatus The HTTP status code.
     * @param  ?string  $httpBody The HTTP body as a string.
     * @param  ?array  $jsonBody The JSON deserialized body.
     * @param  array|CaseInsensitiveArray|null  $httpHeaders The HTTP headers array.
     * @param  ?string  $tapCode The Tap error code.
     *
     * @return static
     */
    public static function factory(
        string $message,
        int $httpStatus = null,
        string $httpBody = null,
        array $jsonBody = null,
        array|CaseInsensitiveArray $httpHeaders = null,
        string $tapCode = null
    ) {
        $instance = new static($message);
        $instance->setHttpStatus($httpStatus);
        $instance->setHttpBody($httpBody);
        $instance->setJsonBody($jsonBody);
        $instance->setHttpHeaders($httpHeaders);
        $instance->setTapCode($tapCode);

        $instance->setRequestId(null);
        if ($httpHeaders && isset($httpHeaders['Request-Id'])) {
            $instance->setRequestId($httpHeaders['Request-Id']);
        }

        $instance->setError($instance->constructErrorObject());

        return $instance;
    }

    /**
     * Gets the Tap error object.
     *
     * @return ErrorObject|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets the Tap error object.
     *
     * @param ErrorObject|null $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Gets the HTTP body as a string.
     *
     * @return ?string
     */
    public function getHttpBody()
    {
        return $this->httpBody;
    }

    /**
     * Sets the HTTP body as a string.
     *
     * @param ?string $httpBody
     */
    public function setHttpBody($httpBody)
    {
        $this->httpBody = $httpBody;
    }

    /**
     * Gets the HTTP headers array.
     *
     * @return array|CaseInsensitiveArray|null
     */
    public function getHttpHeaders(): array|CaseInsensitiveArray|null
    {
        return $this->httpHeaders;
    }

    /**
     * Sets the HTTP headers array.
     */
    public function setHttpHeaders(array|CaseInsensitiveArray $httpHeaders): void
    {
        $this->httpHeaders = $httpHeaders;
    }

    /**
     * Gets the HTTP status code.
     *
     * @return ?int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Sets the HTTP status code.
     *
     * @param ?int $httpStatus
     */
    public function setHttpStatus($httpStatus)
    {
        $this->httpStatus = $httpStatus;
    }

    /**
     * Gets the JSON deserialized body.
     *
     * @return ?array
     */
    public function getJsonBody()
    {
        return $this->jsonBody;
    }

    /**
     * Sets the JSON deserialized body.
     *
     * @param ?array $jsonBody
     */
    public function setJsonBody($jsonBody)
    {
        $this->jsonBody = $jsonBody;
    }

    /**
     * Gets the Tap request ID.
     *
     * @return ?string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Sets the Tap request ID.
     *
     * @param ?string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Gets the Tap error code.
     *
     * Cf. the `CODE_*` constants on {@see \Tap\ErrorObject} for possible
     * values.
     *
     * @return ?string
     */
    public function getTapCode()
    {
        return $this->tapCode;
    }

    /**
     * Sets the Tap error code.
     *
     * @param ?string $tapCode
     */
    public function setTapCode($tapCode)
    {
        $this->tapCode = $tapCode;
    }

    /**
     * Returns the string representation of the exception.
     *
     * @return string
     */
    public function __toString()
    {
        $statusStr = ($this->getHttpStatus() == null) ? "" : "(Status {$this->getHttpStatus()}) ";
        $idStr = ($this->getRequestId() == null) ? "" : "(Request {$this->getRequestId()}) ";
        return "{$statusStr}{$idStr}{$this->getMessage()}";
    }

    protected function constructErrorObject()
    {
        if (is_null($this->jsonBody) || !array_key_exists('error', $this->jsonBody)) {
            return null;
        }

        return ErrorObject::constructFrom($this->jsonBody['error']);
    }
}
