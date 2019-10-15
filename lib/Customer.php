<?php


namespace Tap;

use Tap\Exception\NotExistedMethodException;
use Tap\Util\RequestOptions;

/**
 * Class Customer
 *
 * @property string $object
 * @property bool $live_mode
 * @property string $api_version
 * @property string $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $email
 * @property object $phone
 * @property string $description
 * @property object $metadata
 * @property string $currency
 * @property string $title
 * @property string $nationality
 * @property string discount
 *
 * @package Tap
 */
class Customer extends ApiResource
{
    const OBJECT_NAME = "customer";
    const PATH_BALANCE_TRANSACTIONS = '/balance_transactions';
    const PATH_SOURCES = '/card';
    const PATH_TAX_IDS = '/tax_ids';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * @return void The updated customer.
     */
    public function deleteDiscount()
    {
        $url = $this->instanceUrl() . '/discount';
        $opts = $this->_opts->merge(null);
        $opts = RequestOptions::parse($opts);
        $this->refreshFrom(['discount' => null], $opts, true);
    }

    /**
     * @param string|null $id The ID of the customer on which to create the source.
     * @param $source
     * @param array|string|null $opts
     *
     * @return TapObject|Card
     */
    public static function createSource($id, $source, $opts = null)
    {
        $params = ['source' => $source];
        return self::_createNestedResource($id, static::PATH_SOURCES, $params, $opts);
    }

    /**
     * @param string|null $id The ID of the customer to which the source belongs.
     * @param string|null $sourceId The ID of the source to retrieve.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return TapObject|Card
     */
    public static function retrieveSource($id, $sourceId, $params = null, $opts = null)
    {
        return self::_retrieveNestedResource($id, static::PATH_SOURCES, $sourceId, $params, $opts);
    }

    /**
     * @param string|null $id The ID of the customer to which the source belongs.
     * @param string|null $sourceId The ID of the source to update.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return void
     *
     * @throws NotExistedMethodException
     */
    public static function updateSource($id, $sourceId, $params = null, $opts = null)
    {
        throw new NotExistedMethodException;
//        return self::_updateNestedResource($id, static::PATH_SOURCES, $sourceId, $params, $opts);
    }

    /**
     * @param string|null $id The ID of the customer to which the source belongs.
     * @param string|null $sourceId The ID of the source to delete.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return TapObject|Card
     *
     */
    public static function deleteSource($id, $sourceId, $params = null, $opts = null)
    {
        return self::_deleteNestedResource($id, static::PATH_SOURCES, $sourceId, $params, $opts);
    }

    /**
     * @param string|null $id The ID of the customer on which to retrieve the sources.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Collection
     *
     */
    public static function allSources($id, $params = null, $opts = null)
    {
        return self::_allNestedResources($id, static::PATH_SOURCES, $params, $opts);
    }

    /**
     * @param string|null $id The ID of the customer on which to create the tax id.
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return TapObject
     */
    public static function createTaxId($id, $params = null, $opts = null)
    {
        return self::_createNestedResource($id, static::PATH_TAX_IDS, $params, $opts);
    }
}
