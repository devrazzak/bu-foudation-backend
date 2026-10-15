<?php

namespace App\Enums;

enum BlogStatus: string
{
    case Active = 'active';
    case Inactive = 'Inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive'
        };
    }
}
