<?php

namespace Tap\ApiOperations;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait All
{
    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return \Tap\Collection of ApiResources
     */
    public static function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::classUrl() . '/list';

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \Tap\Util\Util::convertToTapObject($response->json, $opts);
        if (!($obj instanceof \Tap\Collection)) {
            throw new \Tap\Exception\UnexpectedValueException(
                'Expected type ' . \Tap\Collection::class . ', got "' . get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
        $obj->setFilters($params);
        return $obj;
    }
}
