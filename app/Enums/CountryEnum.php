<?php

namespace App\Enums;

enum CountryEnum: string
{

    case Nepal = 'nepal';

    public function customTitle(): string
    {
        return match ($this) {
            CountryEnum::Nepal => 'Nepal',
        };
    }
}
