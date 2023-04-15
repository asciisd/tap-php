<?php


namespace Tap;

/**
 * Class Card
 * @package Tap
 *
 * @property string $id
 * @property boolean $live_mode
 * @property string $api_version
 * @property string $status
 * @property integer $amount
 * @property string $currency
 * @property integer $created
 * @property string $url
 * @property string $timezone
 * @property string $note
 * @property integer $due
 * @property integer $expiry
 * @property string $mode
 * @property object $notifications
 * @property object $order
 * @property object $reference
 * @property object $invoices
 * @property object $customer
 * @property object $post
 * @property object $redirect
 * @property object $metadata
 * @property array $transactions
 * @property array $currencies
 */
class Invoice extends ApiResource
{
    const OBJECT_NAME = "invoice";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
    use ApiOperations\Delete;
}
