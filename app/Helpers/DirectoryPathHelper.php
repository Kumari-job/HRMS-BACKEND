<?php

namespace App\Helpers;

class DirectoryPathHelper
{

    public static function citizenshipFrontDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/citizenships/front";
    }

    public static function citizenshipBackDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/citizenships/back";
    }

    public static function drivingLicenseDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/driving_licenses";
    }

    public static function passportDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/passports";
    }

    public static function experienceDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/experiences";
    }

    public static function educationDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/education";
    }
    public static function panCardDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/pan_cards";
    }
    public static function employeeImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/images";
    }

    public static function warrantyImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/assets/warranty/image";
    }
    public static function guaranteeImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/assets/guarantee/image";
    }
}
