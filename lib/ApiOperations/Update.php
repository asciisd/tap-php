<?php

namespace Tap\ApiOperations;

use Tap\Exception\InvalidArgumentException;

/**
 * Trait for updatable resources. Adds an `update()` static method and a
 * `save()` method to the class.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait Update
{
    /**
     * @param string $id The ID of the resource to update.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return array|\Tap\TapObject The updated resource.
     */
    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('put', $url, $params, $opts);
        $obj = \Tap\Util\Util::convertToTapObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }

    /**
     * @param array|string|null $opts
     *
     * @return static The saved resource.
     */
    public function save($opts = null)
    {
        $params = $this->jsonSerialize();
        if (count($params) > 0) {
            $url = $this->instanceUrl();
            list($response, $opts) = $this->_request('put', $url, $params, $opts);
            $this->refreshFrom($response, $opts);
        }
        return $this;
    }
}
