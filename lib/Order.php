<?php


namespace Tap;

/**
 * Class Card
 * @package Tap
 *
 * @property string $id
 * @property string $object
 * @property string $currency
 * @property integer $amount
 * @property integer $created
 * @property array $items
 * @property array $tax
 * @property object $shipping
 */
class Order extends ApiResource
{
    const OBJECT_NAME = "order";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
    use ApiOperations\Delete;
}
