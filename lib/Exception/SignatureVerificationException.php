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
    protected mixed $httpBody;
    protected mixed $sigHeader;
    protected mixed $expectedSignature;
    protected mixed $currentSignature;

    /**
     * Creates a new SignatureVerificationException exception.
     *
     * @param $message
     * @param $httpBody
     * @param $sigHeader
     * @param $expectedSignature
     * @param $currentSignature
     * @return SignatureVerificationException
     */
    public static function factory(
        $message,
        $httpBody = null,
        $sigHeader = null,
        $expectedSignature = null,
        $currentSignature = null
    ): SignatureVerificationException
    {
        $instance = new static($message);
        $instance->setHttpBody($httpBody);
        $instance->setSigHeader($sigHeader);
        $instance->setExpectedSignature($expectedSignature);
        $instance->setCurrentSignature($currentSignature);

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
    public function setSigHeader($sigHeader): void
    {
        $this->sigHeader = $sigHeader;
    }

    private function setExpectedSignature(mixed $expectedSignature): void
    {
        $this->expectedSignature = $expectedSignature;
    }

    private function setCurrentSignature(mixed $currentSignature): void
    {
        $this->currentSignature = $currentSignature;
    }
}
