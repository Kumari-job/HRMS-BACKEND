<?php

namespace App\Enums;

enum MaritalStatusEnum: string
{
    case Single = 'single';
    case Married = 'married';
    case Widowed = 'widowed';
    case Divorced = 'divorced';
}