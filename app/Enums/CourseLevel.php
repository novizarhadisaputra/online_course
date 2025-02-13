<?php

namespace App\Enums;

enum CourseLevel: string
{
    case Beginner = 'beginner';
    case Middle = 'middle';
    case Advance = 'advance';

    public function getLabel(): ?string
    {
        return $this->name;

        return match ($this) {
            self::Beginner => 'beginner',
            self::Middle => 'middle',
            self::Advance => 'advance',
        };
    }
}
