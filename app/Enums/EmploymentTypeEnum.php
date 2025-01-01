<?php

namespace App\Enums;

enum EmploymentTypeEnum: string
{

    case Permanent = 'permanent';
    case PartTime = 'part_time';
    case FullTime = 'full_time';
    public function customTitle(): string
    {
        return match ($this) {
            EmploymentTypeEnum::Permanent => 'Permanent',
            EmploymentTypeEnum::PartTime => 'Part Time Regular',
            EmploymentTypeEnum::FullTime => 'Full Time Regular',
        };
    }
}
