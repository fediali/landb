<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static ShippingStatusEnum NOT_APPROVED()
 * @method static ShippingStatusEnum APPROVED()
 * @method static ShippingStatusEnum PICKING()
 * @method static ShippingStatusEnum DELAY_PICKING()
 * @method static ShippingStatusEnum PICKED()
 * @method static ShippingStatusEnum NOT_PICKED()
 * @method static ShippingStatusEnum DELIVERING()
 * @method static ShippingStatusEnum DELIVERED()
 * @method static ShippingStatusEnum NOT_DELIVERED()
 * @method static ShippingStatusEnum AUDITED()
 * @method static ShippingStatusEnum CANCELED()
 * @method static ShippingStatusEnum SHIPMENT_COMPLETED()
 */
class ShippingStatusEnum extends Enum
{
    public const NOT_APPROVED = 'not_approved';
    public const APPROVED = 'approved';
    public const PICKING = 'picking';
    public const DELAY_PICKING = 'delay_picking';
    public const PICKED = 'picked';
    public const NOT_PICKED = 'not_picked';
    public const DELIVERING = 'delivering';
    public const DELIVERED = 'delivered';
    public const NOT_DELIVERED = 'not_delivered';
    public const AUDITED = 'audited';
    public const CANCELED = 'canceled';
    public const SHIPMENT_COMPLETED = 'shipment_completed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/ecommerce::shipping.statuses';
}
