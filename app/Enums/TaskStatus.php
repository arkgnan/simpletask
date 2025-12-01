<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = "pending";
    case OPEN = "open";
    case IN_PROGRESS = "in_progress";
    case COMPLETED = "completed";

    public static function default(): self
    {
        return self::PENDING;
    }

    public static function values(): array
    {
        return array_column(self::cases(), "value");
    }
}
