<?php

namespace App\Enums;

enum AssetStatusEnum: string
{
    case New = 'new';
    case Used = 'used';
    case Damaged = 'damaged';
    case Maintenance = 'casual';
    case Disposed = 'disposed';

}
