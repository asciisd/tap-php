<?php


namespace Tap;

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

    use ApiOperations\All;
    use ApiOperations\Update;
    use ApiOperations\NestedResource;
    use ApiOperations\Delete;

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
        $parentExtn = urlencode(Util\Util::utf8($parent));
        $extn = urlencode(Util\Util::utf8($this['id']));
        return "$base/$parentExtn/$path/$extn";
    }

    /**
     * @param array|string $_id
     * @param array|string|null $_opts
     *
     * @throws \Tap\Exception\BadMethodCallException
     */
    public static function retrieve($_id, $_opts = null)
    {
        $msg = "Cards cannot be retrieved without a customer ID or an " .
            "account ID. Retrieve a card using " .
            "`Customer::retrieveSource('customer_id', 'card_id')` or " .
            "`Account::retrieveExternalAccount('account_id', 'card_id')`.";
        throw new Exception\BadMethodCallException($msg);
    }

    /**
     * @param string $_id
     * @param array|null $_params
     * @param array|string|null $_options
     *
     * @throws \Tap\Exception\BadMethodCallException
     */
    public static function update($_id, $_params = null, $_options = null)
    {
        $msg = "Cards cannot be updated without a customer ID or an " .
            "account ID. Update a card using " .
            "`Customer::updateSource('customer_id', 'card_id', " .
            "\$updateParams)` or `Account::updateExternalAccount(" .
            "'account_id', 'card_id', \$updateParams)`.";
        throw new Exception\BadMethodCallException($msg);
    }
}
