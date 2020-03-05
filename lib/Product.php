<?php


namespace Tap;

/**
 * Class Card
 * @package Tap
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $currency
 * @property integer $amount
 * @property integer $quantity
 * @property object $discount
 */
class Product extends ApiResource
{
    const OBJECT_NAME = "product";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
    use ApiOperations\Delete;
}
