<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PricingUnit: string implements HasLabel, HasColor
{
    case Hour = 'h';
    case Day = 'd';
    case Project = 'p';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Hour => __('perHour'),
            self::Day => __('perDay'),
            self::Project => __('perProject'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Hour => 'blue',
            self::Day => 'teal',
            self::Project => 'purple',
        };
    }
}
