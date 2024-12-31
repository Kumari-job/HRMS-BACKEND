<?php

namespace App\Helpers;

class MessageHelper
{
    public static function getErrorMessage(string $type = 'form'): string
    {
        $errors = [
            'form' => 'There are some issues in form.',
            'server-error' => 'Something went wrong.',
        ];

        return $errors[$type];
    }
}
