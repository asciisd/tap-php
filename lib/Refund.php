<?php


namespace Tap;

/**
 * Class Refund
 *
 * @property string $id
 * @property string $object
 * @property string $api_version
 * @property boolean $live_mode
 * @property int $amount
 * @property string $charge_id
 * @property string $created
 * @property string $currency
 * @property string $status
 * @property string $description
 * @property string $reason
 * @property string $balance_transaction
 *
 * @package Tap
 */
class Refund extends ApiResource
{
    const OBJECT_NAME = "refund";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * Possible string representations of the failure reason.
     * @link https://tappayments.api-docs.io/2.0/refunds/refund-request
     */
    const FAILURE_REASON = 'expired_or_canceled_card';
    const FAILURE_REASON_LOST_OR_STOLEN_CARD = 'lost_or_stolen_card';
    const FAILURE_REASON_UNKNOWN = 'unknown';
    /**
     * Possible string representations of the refund reason.
     * @link https://tappayments.api-docs.io/2.0/refunds/refund-request
     */
    const REASON_DUPLICATE = 'duplicate';
    const REASON_FRAUDULENT = 'fraudulent';
    const REASON_REQUESTED_BY_CUSTOMER = 'requested_by_customer';
    /**
     * Possible string representations of the refund status.
     * @link https://tappayments.api-docs.io/2.0/refunds/refund-request
     */
    const STATUS_SUCCEEDED = 'REFUNDED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_CANCELED = 'CANCELLED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_RESTRICTED = 'RESTRICTED';
    const STATUS_TIMEDOUT = 'TIMEDOUT';
    const STATUS_UNKNOWN = 'UNKNOWN';
}
