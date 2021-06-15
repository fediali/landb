<?php

namespace Botble\Base\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static BaseStatusEnum DRAFT()
 * @method static BaseStatusEnum PUBLISHED()
 * @method static BaseStatusEnum PENDING()
 * @method static BaseStatusEnum HIDDEN()
 * @method static BaseStatusEnum ACTIVE()
 * @method static BaseStatusEnum DISABLED()
 */
class BaseStatusEnum extends Enum
{
    public const PUBLISHED = 'published';
    public const DRAFT = 'draft';
    public const PENDING = 'pending';

    public const SCHEDULE = 'schedule';


    public const HIDDEN = 'hidden';
    public const ACTIVE = 'active';
    public const DISABLED = 'disabled';


    public static $SCHEDULE = [
        'schedule'  => self::SCHEDULE,
        'published' => self::PUBLISHED,

    ];
    public static $DEFAULT = [
        'published' => self::PUBLISHED,
        'draft'     => self::DRAFT,
        'pending'   => self::PENDING,
    ];
    public static $STATUSES = [
        'H' => self::HIDDEN,
        'A' => self::ACTIVE,
        'D' => self::DISABLED,
    ];

    /**
     * @var string
     */
    public static $langPath = 'core/base::enums.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::DRAFT || self::HIDDEN :
                return Html::tag('span', self::DRAFT()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::PENDING || self::DISABLED :
                return Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::PUBLISHED || self::ACTIVE:
                return Html::tag('span', self::PUBLISHED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
