<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case WAITING_PAYMENT = 'waiting payment';
    case REFUND = 'refund';
    case SUCCESS = 'success';
    case CANCEL = 'cancel';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::WAITING_PAYMENT => 'waiting payment',
            self::REFUND => 'refund',
            self::SUCCESS => 'success',
            self::CANCEL => 'cancel',
        };
    }
}
