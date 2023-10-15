<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: string implements HasLabel, HasColor
{
    case CREATED = 'created';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';

    public function getLabel(): ?string
    {
        return match($this) {
            self::CREATED => 'Created',
            self::CONFIRMED => 'Confirmed',
            self::CANCELED => 'Canceled',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::CREATED => 'warnin',
            self::CONFIRMED => 'success',
            self::CANCELED => 'danger'
        };
    }
}
