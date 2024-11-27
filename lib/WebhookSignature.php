<?php

namespace Tap;

use Tap\Exception\SignatureVerificationException;

abstract class WebhookSignature
{
    const EXPECTED_SCHEME = 'hashstring';

    /**
     * Verifies the signature header sent by Tap. Throws an
     * Exception\SignatureVerificationException exception if the verification fails for
     * any reason.
     *
     * @param array $payload the payload sent by Tap
     * @param array $header the contents of the signature header sent by
     *  Tap
     * @param string $secret secret used to generate the signature
     * @param int $tolerance maximum difference allowed between the header's
     *  timestamp and the current time
     *
     * @return bool
     * @throws SignatureVerificationException if the verification fails
     *
     */
    public static function verifyHeader($payload, $header, $secret, $tolerance = null)
    {
        // Extract timestamp and signatures from header
        $signature = self::getSignature($header, self::EXPECTED_SCHEME);

        if (empty($signature)) {
            throw SignatureVerificationException::factory(
                'No signature found with expected scheme',
                $payload,
                $header
            );
        }

        // Check if expected signature is found in list of signatures from
        // header
        $expectedSignature = self::computeSignature($payload, $secret);
        $signatureFound = false;

        if ($expectedSignature == $signature) {
            $signatureFound = true;
        }

        if (!$signatureFound) {
            throw SignatureVerificationException::factory(
                'No signatures found matching the expected signature for payload', $payload, $header, $expectedSignature, $signature
            );
        }

        return true;
    }

    /**
     * Extracts the signatures matching a given scheme in a signature header.
     *
     * @param array $header the signature header
     * @param string $scheme the signature scheme to look for
     *
     * @return string the signature matching the provided scheme
     */
    private static function getSignature($header, $scheme)
    {
        $signature = '';

        foreach ($header as $key => $value) {
            if ($key === $scheme) {
                $signature = $value[0];
            }
        }

        return $signature;
    }

    /**
     * Computes the signature for a given payload and secret.
     *
     * The current scheme used by Tap ("v1") is HMAC/SHA-256.
     *
     * @param array $payload the payload to sign
     * @param string $secret the secret used to generate the signature
     *
     * @return string the signature as a string
     */
    private static function computeSignature($payload, $secret)
    {
        return hash_hmac('sha256', self::generateSignature($payload), $secret);
    }

    /**
     * @param array $payload
     * @return string
     */
    private static function generateSignature($payload)
    {
        $object = $payload['object'];

        $id = $payload['id'];
        $currency = $payload['currency'];
        $amount = number_format($payload['amount'], $currency == 'KWD' ? 3 : 2);
        $gateway_reference = $payload['reference']['gateway'];
        $payment_reference = $payload['reference']['payment'];
        $updated = $payload['updated'] ?? null;
        $status = $payload['status'];
        $created = $payload['transaction']['created'];

        if ($object === 'invoice') {
            $toBeHashedString = 'x_id' . $id . 'x_amount' . $amount . 'x_currency' . $currency . 'x_updated' . $updated . 'x_status' . $status . 'x_created' . $created . '';
        } else {
            // Charge or Authorize - Create a hashstring from the posted response data + the data that are related to you.
            $toBeHashedString = 'x_id' . $id . 'x_amount' . $amount . 'x_currency' . $currency . 'x_gateway_reference' . $gateway_reference . 'x_payment_reference' . $payment_reference . 'x_status' . $status . 'x_created' . $created . '';
        }

        return $toBeHashedString;
    }
}
