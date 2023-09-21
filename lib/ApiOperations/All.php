<?php

namespace Tap\ApiOperations;

use Tap\Collection;
use Tap\Exception\UnexpectedValueException;
use Tap\TapObject;
use Tap\Util\Util;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait All
{
    public static function all(array $params = null, array|string $opts = null): array|TapObject
    {
        self::_validateParams($params);
        $url = static::classUrl() . '/list';

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util::convertToTapObject($response->json, $opts);
        if (!($obj instanceof TapObject)) {
            throw new UnexpectedValueException(
                'Expected type ' . Collection::class . ', got "' . get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
        return $obj;
    }
}
