<?php

namespace App\Helpers;

class DirectoryPathHelper
{

    public static function citizenshipFrontDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/citizenships/front";
    }

    public static function citizenshipBackDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/citizenships/back";
    }

    public static function employeeImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/images";
    }
}
