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

    public static function drivingLicenseDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/driving_licenses";
    }

    public static function passportDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/passports";
    }

    public static function experienceDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employee-experiences";
    }
    public static function panCardDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/pan_cards";
    }
    public static function employeeImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/images";
    }
}
