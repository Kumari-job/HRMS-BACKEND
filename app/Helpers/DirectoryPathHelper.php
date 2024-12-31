<?php

namespace App\Helpers;

class DirectoryPathHelper
{

    public static function citizenshipFrontDirectoryPath(int $company_id,$employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/". $employee_id ."/citizenship/front";
    }

    public static function citizenshipBackDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/". $employee_id ."/citizenship/back";
    }

    public static function drivingLicenseDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/". $employee_id."/driving_licenses";
    }

    public static function passportDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/". $employee_id."/passports";
    }

    public static function experienceDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/".$employee_id."/experiences";
    }

    public static function educationDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/".$employee_id."/education";
    }
    public static function panCardDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/".$employee_id."/pan_cards";
    }
    public static function employeeImageDirectoryPath(int $company_id, $employee_id): string
    {
        return "companies/company_" . $company_id . "/employees/".$employee_id."/images";
    }

    public static function warrantyImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/assets/warranty/image";
    }
    public static function guaranteeImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/assets/guarantee/image";
    }

    public static function employeeImportDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/employees/import";
    }

    public static function assetImageDirectoryPath(int $company_id): string
    {
        return "companies/company_" . $company_id . "/assets/image";
    }
}
