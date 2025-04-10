<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CouponType: string implements HasLabel
{
    case GENERAL = 'general';
    case SPECIFIC_COURSE = 'specific course';
    case SPECIFIC_CATEGORY = 'specific category';
    case SPECIFIC_USER = 'specific user';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::GENERAL => 'general',
            self::SPECIFIC_COURSE => 'specific course',
            self::SPECIFIC_CATEGORY => 'specific category',
            self::SPECIFIC_USER => 'specific user',
        };
    }
}
