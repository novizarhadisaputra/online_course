<?php

namespace App\Enums;

enum TransactionCategory: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::DEBIT => 'debit',
            self::CREDIT => 'credit',
        };
    }
}
