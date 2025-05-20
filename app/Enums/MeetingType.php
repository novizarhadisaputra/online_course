<?php

namespace App\Enums;

enum MeetingType: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::ONLINE => 'online',
            self::OFFLINE => 'offline',
        };
    }
}
