<?php


namespace Tap;

/**
 * Class Token
 * @package Tap
 *
 * @property string $id
 * @property string $object
 * @property string $client_ip
 * @property int $created
 * @property bool $livemode
 * @property string $type
 * @property bool $used
 * @property Card $card
 */
class Token extends ApiResource
{
    const OBJECT_NAME = "token";

    use ApiOperations\Create;
    use ApiOperations\Retrieve;

    /**
     * Possible string representations of the token type.
     * @link https://tap.company/docs/api/tokens/object#token_object-type
     */
    const TYPE_ACCOUNT      = 'account';
    const TYPE_BANK_ACCOUNT = 'bank_account';
    const TYPE_CARD         = 'card';
    const TYPE_PII          = 'pii';

}
