<?php

namespace Tap\ApiOperations;

use Tap\TapObject;
use Tap\Util\Util;

/**
 * Trait for creatable resources. Adds a `create()` static method to the class.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait Create
{
    /**
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return array|TapObject|\Tap\Customer The created resource.
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util::convertToTapObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }
}
