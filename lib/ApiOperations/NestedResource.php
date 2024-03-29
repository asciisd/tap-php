<?php

namespace Tap\ApiOperations;

/**
 * Trait for resources that have nested resources.
 *
 * This trait should only be applied to classes that derive from TapObject.
 */
trait NestedResource
{
    /**
     * @param string $method
     * @param string $url
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject
     */
    protected static function _nestedResourceOperation($method, $url, $params = null, $options = null)
    {
        self::_validateParams($params);

        list($response, $opts) = static::_staticRequest($method, $url, $params, $options);
        $obj = \Tap\Util\Util::convertToTapObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?string $nestedId
     *
     * @return string
     */
    protected static function _nestedResourceUrl($id, $nestedPath, $nestedId = null)
    {
        $url = '/v2' . $nestedPath . static::nestedResourceUrl($id);
        if ($nestedId !== null) {
            $url .= "/$nestedId";
        }
        return $url;
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject
     */
    protected static function _createNestedResource($id, $nestedPath, $params = null, $options = null)
    {
        $url = static::_nestedResourceUrl($id, $nestedPath);
        return self::_nestedResourceOperation('post', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?string $nestedId
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject
     */
    protected static function _retrieveNestedResource($id, $nestedPath, $nestedId, $params = null, $options = null)
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);
        return self::_nestedResourceOperation('get', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?string $nestedId
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject
     */
    protected static function _updateNestedResource($id, $nestedPath, $nestedId, $params = null, $options = null)
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);
        return self::_nestedResourceOperation('post', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?string $nestedId
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject
     */
    protected static function _deleteNestedResource($id, $nestedPath, $nestedId, $params = null, $options = null)
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);
        return self::_nestedResourceOperation('delete', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param ?array $params
     * @param ?array|?string $options
     *
     * @return \Tap\TapObject | \Tap\Collection
     */
    protected static function _allNestedResources($id, $nestedPath, $params = null, $options = null)
    {
        $url = static::_nestedResourceUrl($id, $nestedPath);
        return self::_nestedResourceOperation('get', $url, $params, $options);
    }
}
