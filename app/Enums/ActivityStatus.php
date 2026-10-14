<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Ongoing = 'ongoing';
    case Upcoming = 'upcoming';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Ongoing => 'Ongoing',
            self::Upcoming => 'Upcoming',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
