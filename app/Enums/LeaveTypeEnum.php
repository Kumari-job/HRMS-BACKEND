<?php

namespace App\Enums;

enum LeaveTypeEnum: string
{
    case Full = 'full';
    case Half = 'half';
    case Quarter = 'quarter';
}