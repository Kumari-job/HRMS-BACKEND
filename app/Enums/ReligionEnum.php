<?php

namespace App\Enums;

enum ReligionEnum: string
{
    case Hinduism = 'hinduism';
    case Buddhism = 'buddhism';
    case Christianity = 'christianity';
    case Islam = 'islam';
    case Sikhism = 'sikhism';
    case Judaism = 'judaism';
    case Atheism = 'atheism';
    case Other = 'other';

    public function customTitle(): string
    {
        return match ($this) {
            ReligionEnum::Hinduism => 'Hinduism',
            ReligionEnum::Buddhism => 'Buddhism',
            ReligionEnum::Christianity => 'Christianity',
            ReligionEnum::Islam => 'Islam',
            ReligionEnum::Sikhism => 'Sikhism',
            ReligionEnum::Judaism => 'Judaism',
            ReligionEnum::Atheism => 'Atheism',
            ReligionEnum::Other => 'Other',
        };
    }
}
