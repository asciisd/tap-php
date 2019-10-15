<?php


namespace Tap;

/**
 * Class Authorize
 *
 * @property string $id
 * @property string $object
 * @property boolean $live_mode
 * @property string $api_version
 * @property string $status
 * @property integer $amount
 * @property string $currency
 * @property boolean $threeDSecure
 * @property boolean $save_card
 * @property string $description
 * @property string $statement_descriptor
 * @property object $metadata
 * @property object $transaction
 * @property object $reference
 * @property object $response
 * @property object $receipt
 * @property Customer $customer
 * @property object $source
 * @property object $auto
 * @property object $void
 * @property object $capture
 * @property object $security
 * @property object $acquirer
 * @property Card $card
 * @property object $airline
 * @property object $post
 * @property object $redirect
 *
 *
 * @package Tap
 */
class Authorize extends ApiResource
{
    const OBJECT_NAME = "authorize";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Authorize void The captured charge.
     * @throws Exception\ApiErrorException
     */
    public function void($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/void';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @return string The endpoint URL for the given class.
     */
    public static function classUrl()
    {
        // Replace dots with slashes for namespaced resources, e.g. if the object's name is
        // "foo.bar", then its URL will be "/v1/foo/bars".
        $base = str_replace('.', '/', static::OBJECT_NAME);
        return "/v2/${base}";
    }

}
