<?php

namespace App\Helpers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class DateHelper
{
    public static function englishToNepali($englishDate, string $format = 'F d, Y',  string $locale = 'en')
    {
        $nepaliDate = LaravelNepaliDate::from($englishDate)->toNepaliDate(format: $format, locale: $locale);
        return $nepaliDate;
    }

    public static function nepaliToEnglish($nepaliDate)
    {
        $englishDate = LaravelNepaliDate::from($nepaliDate)->toEnglishDate();
        return $englishDate;
    }
}
