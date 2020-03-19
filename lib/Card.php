<?php


namespace Tap;

use Tap\Exception\UnexpectedValueException;
use Tap\Util\Util;

/**
 * Class Card
 * @package Tap
 *
 * @property string $id
 * @property string $object
 * @property object $address
 * @property string $customer
 * @property string $funding
 * @property string $fingerprint
 * @property string $brand
 * @property integer $exp_month
 * @property integer $exp_year
 * @property string $last_four
 * @property string $first_six
 * @property string $name
 */
class Card extends ApiResource
{
    const OBJECT_NAME = "card";
    const PATH_SOURCES = '/card';

    use ApiOperations\NestedResource;


    /**
     * @param $customer_id
     * @param array|string|null $opts
     *
     * @return array|TapObject
     * @throws Exception\ApiErrorException
     */
    public static function all($customer_id, $opts = null)
    {
        $url = static::classUrl() . '/' . $customer_id;

        list($response, $opts) = static::_staticRequest('get', $url, null, $opts);
        $obj = Util::convertToTapObject($response->json, $opts);
        if (!($obj instanceof TapObject)) {
            throw new UnexpectedValueException(
                'Expected type ' . Collection::class . ', got "' . get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
//        $obj->setFilters($params);
        return $obj;
    }

    /**
     * @param string|null $id The ID of the customer on which to create the source.
     * @param $source
     * @param array|string|null $opts
     *
     * @return TapObject|Card
     */
    public static function createFromSource($id, $source, $opts = null)
    {
        $params = ['source' => $source];
        return self::_createNestedResource($id, static::PATH_SOURCES, $params, $opts);
    }

    /**
     * find more information at https://tappayments.api-docs.io/2.0/cards/verify-a-card
     *
     * @param $params
     * @param null $options
     * @return array|TapObject|\Tap\Card The created resource.
     * @throws Exception\ApiErrorException
     */
    public static function verify($params, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl() . '/verify';

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util::convertToTapObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }

    public static function classUrl()
    {
        // Replace dots with slashes for namespaced resources, e.g. if the object's name is
        // "foo.bar", then its URL will be "/v1/foo/bars".
        $base = str_replace('.', '/', static::OBJECT_NAME);
        return "/v2/${base}";
    }

    /**
     * @return string The instance URL for this resource. It needs to be special
     *    cased because cards are nested resources that may belong to different
     *    top-level resources.
     */
    public function instanceUrl()
    {
        if ($this['customer']) {
            $base = Customer::classUrl();
            $parent = $this['customer'];
            $path = 'sources';
        } else {
            $msg = "Cards cannot be accessed without a customer ID, account ID or recipient ID.";
            throw new Exception\UnexpectedValueException($msg);
        }
        $parentExtn = urlencode(Util::utf8($parent));
        $extn = urlencode(Util::utf8($this['id']));
        return "$base/$parentExtn/$path/$extn";
    }

    /**
     * @param array|string $card_id
     * @param $customer_id
     * @param array|string|null $_opts
     *
     * @return Card|TapObject
     */
    public static function retrieve($card_id, $customer_id, $_opts = null)
    {
        return Customer::retrieveSource($customer_id, $card_id);
    }

    /**
     * @param $card_id
     * @param $customer_id
     * @param null $params
     * @throws Exception\NotExistedMethodException
     */
    public static function update($card_id, $customer_id, $params = null)
    {
        Customer::updateSource($customer_id, $card_id, $params);
    }

    /**
     * delete saved source
     *
     * @param $card_id
     * @param $customer_id
     * @return Card|TapObject
     */
    public static function delete($card_id, $customer_id)
    {
        return Customer::deleteSource($customer_id, $card_id);
    }
}
