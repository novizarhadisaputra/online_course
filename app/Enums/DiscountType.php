<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DiscountType: string implements HasLabel
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::PERCENT => 'percent',
            self::FIXED => 'fixed',
        };
    }
}
