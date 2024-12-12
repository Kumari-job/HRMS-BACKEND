<?php

namespace App\Enums;

enum ContractTypeEnum: string
{
    case FixedTerm = 'fixed_term';
    case PartTime = 'part_time';
    case Temporary = 'temporary';
    case Casual = 'casual';
    case Internship = 'internship';
    case Traineeship = 'traineeship';
    case RemoteWork = 'remote_work';

    public function customTitle(): string
    {
        return match ($this) {
            ContractTypeEnum::FixedTerm => 'Fixed Term',
            ContractTypeEnum::PartTime => 'Part Time',
            ContractTypeEnum::Temporary => 'Temporary',
            ContractTypeEnum::Casual => 'Casual',
            ContractTypeEnum::Internship => 'Internship',
            ContractTypeEnum::Traineeship => 'Traineeship',
            ContractTypeEnum::RemoteWork => 'Remote Work',
        };
    }
}
