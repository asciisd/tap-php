<?php


namespace Tap;

/**
 * Class Card
 * @package Tap
 *
 * @property string $id
 * @property string $object
 * @property boolean $live_mode
 * @property string $api_version
 * @property string $status
 * @property boolean $auto_renew
 * @property object $term
 * @property object $due
 * @property object $grace
 * @property object $expiry
 * @property object $penalty
 * @property object $invoice
 * @property integer $created
 * @property array $generated_invoices
 */
class Recurring extends ApiResource
{
    const OBJECT_NAME = "recurring";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
    use ApiOperations\Delete;

    /**
     * @return string The endpoint URL for the given class.
     */
    public static function classUrl()
    {
        // Replace dots with slashes for namespaced resources, e.g. if the object's name is
        // "foo.bar", then its URL will be "/v1/foo/bars".
        $base = str_replace('.', '/', static::OBJECT_NAME);
        return "/v2/{$base}";
    }
}
