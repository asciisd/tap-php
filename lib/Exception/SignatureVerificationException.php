<?php

namespace Tap\Exception;

/**
 * SignatureVerificationException is thrown when the signature verification for
 * a webhook fails.
 *
 * @package Tap\Exception
 */
class SignatureVerificationException extends \Exception implements ExceptionInterface
{
    protected $httpBody;
    protected $sigHeader;

    /**
     * Creates a new SignatureVerificationException exception.
     *
     * @param string $message The exception message.
     * @param ?string $httpBody The HTTP body as a string.
     * @param ?string $sigHeader The `Tap-Signature` HTTP header.
     *
     * @return SignatureVerificationException
     */
    public static function factory(
        $message,
        $httpBody = null,
        $sigHeader = null
    ) {
        $instance = new static($message);
        $instance->setHttpBody($httpBody);
        $instance->setSigHeader($sigHeader);

        return $instance;
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
     * Gets the `Tap-Signature` HTTP header.
     *
     * @return ?string
     */
    public function getSigHeader()
    {
        return $this->sigHeader;
    }

    /**
     * Sets the `Tap-Signature` HTTP header.
     *
     * @param ?string $sigHeader
     */
    public function setSigHeader($sigHeader)
    {
        $this->sigHeader = $sigHeader;
    }
}
