<?php

namespace App\Enums;

enum BloodGroupEnum: string
{
    case APositive = 'a_positive';
    case ANegative = 'a_negative';
    case BPositive = 'b_positive';
    case BNegative = 'b_negative';
    case ABPositive = 'ab_positive';
    case ABNegative = 'ab_negative';
    case OPositive = 'o_positive';
    case ONegative = 'o_negative';

    public function customTitle(): string
    {
        return match ($this) {
            BloodGroupEnum::APositive => 'A+',
            BloodGroupEnum::ANegative => 'A-',
            BloodGroupEnum::BPositive => 'B+',
            BloodGroupEnum::BNegative => 'B-',
            BloodGroupEnum::ABPositive => 'AB+',
            BloodGroupEnum::ABNegative => 'AB-',
            BloodGroupEnum::OPositive => 'O+',
            BloodGroupEnum::ONegative => 'O-',
        };
    }
}
