<?php


namespace Tap;

/**
 * Class Charge
 *
 * @property string $object
 * @property boolean $live_mode
 * @property string $api_version
 * @property string $id
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
 * @property object $customer
 * @property object $source
 * @property object $security
 * @property object $acquirer
 * @property object $card
 * @property object $airline
 * @property object $destinations
 * @property object $application
 * @property object $merchant_payouts
 * @property object $post
 * @property object $redirect
 *
 *
 * @package Tap
 */
class Charge extends ApiResource
{
    const OBJECT_NAME = "charge";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Charge The captured charge.
     * @throws Exception\ApiErrorException
     */
    public function capture($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/capture';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }

}
