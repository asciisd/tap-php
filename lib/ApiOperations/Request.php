<?php

namespace Tap\ApiOperations;

use Tap\ApiRequestor;
use Tap\Exception\ApiErrorException;
use Tap\Exception\InvalidArgumentException;
use Tap\Util\RequestOptions;

/**
 * Trait for resources that need to make API requests.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait Request
{
    /**
     * @param ?mixed $params The list of parameters to validate
     *
     * @throws InvalidArgumentException if $params exists and is not an array
     */
    protected static function _validateParams(mixed $params = null): void
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Tap API "
               . "method calls.  (HINT: an example call to create a charge "
               . "would be: \"Tap\\Charge::create(['amount' => 100, "
               . "'currency' => 'usd', 'source' => 'tok_1234'])\")";
            throw new InvalidArgumentException($message);
        }
    }

    protected static function _validateRequired($params, $required_vars)
    {
        if ($params == [] && $required_vars != []) {
            throw new InvalidArgumentException($required_vars[0] . ' is required');
        } else {
            foreach ($required_vars as $key) {
                if (!array_key_exists($key, $params)) {
                    throw new InvalidArgumentException($key . ' is required');
                }
            }
        }
    }

    /**
     * @param string $method HTTP method ('get', 'post', etc.)
     * @param string $url URL for the request
     * @param array $params list of parameters for the request
     * @param ?array|?string $options
     *
     * @throws ApiErrorException if the request fails
     *
     * @return array tuple containing (the JSON response, $options)
     */
    protected function _request($method, $url, $params = [], $options = null)
    {
        $opts = $this->_opts->merge($options);
        list($resp, $options) = static::_staticRequest($method, $url, $params, $opts);
        $this->setLastResponse($resp);
        return [$resp->json, $options];
    }

    /**
     * @param string $method HTTP method ('get', 'post', etc.)
     * @param string $url URL for the request
     * @param array $params list of parameters for the request
     * @param ?array|?string $options
     *
     * @throws ApiErrorException if the request fails
     *
     * @return array tuple containing (the JSON response, $options)
     */
    protected static function _staticRequest($method, $url, $params, $options)
    {
        $opts = RequestOptions::parse($options);
        $baseUrl = isset($opts->apiBase) ? $opts->apiBase : static::baseUrl();
        $requester = new ApiRequestor($opts->apiKey, $baseUrl);
        list($response, $opts->apiKey) = $requester->request($method, $url, $params, $opts->headers);
        $opts->discardNonPersistentHeaders();
        return [$response, $opts];
    }
}
