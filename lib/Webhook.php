<?php

namespace Tap;

abstract class Webhook
{
    const DEFAULT_TOLERANCE = 300;

    /**
     * Returns an Event instance using the provided JSON payload. Throws an
     * Exception\UnexpectedValueException if the payload is not valid JSON, and
     * an Exception\SignatureVerificationException if the signature
     * verification fails for any reason.
     *
     * @param array $payload the payload sent by Stripe
     * @param array $sigHeader the contents of the signature header sent by
     *  Stripe
     * @param string $secret secret used to generate the signature
     * @param int $tolerance maximum difference allowed between the header's
     *  timestamp and the current time
     *
     * @return array
     * @throws Exception\SignatureVerificationException if the verification fails
     *
     * @throws Exception\UnexpectedValueException if the payload is not valid JSON,
     */
    public static function constructEvent($payload, $sigHeader, $secret, $tolerance = self::DEFAULT_TOLERANCE)
    {
        WebhookSignature::verifyHeader($payload, $sigHeader, $secret, $tolerance);

        if (null === $payload) {
            $msg = "Invalid payload: {$payload} ";

            throw new Exception\UnexpectedValueException($msg);
        }

        return $payload;
    }
}
