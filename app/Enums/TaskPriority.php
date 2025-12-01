<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = "low";
    case MEDIUM = "medium";
    case HIGH = "high";

    /**
     * Get the default priority
     */
    public static function default(): self
    {
        return self::LOW;
    }

    /**
     * Get all values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), "value");
    }
}
