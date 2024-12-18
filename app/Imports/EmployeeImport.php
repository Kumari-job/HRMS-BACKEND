<?php

namespace App\Imports;

use App\Helpers\DateHelper;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsErrors, SkipsFailures;

    public function collection(Collection $rows)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        foreach ($rows as $row) {

            $dateOfBirth = is_numeric($row['date_of_birth'])
                ? Date::excelToDateTimeObject($row['date_of_birth'])->format('d/m/Y')
                : $row['date_of_birth'];

            Validator::make([
                'name' => $row['name'],
                'email' => $row['email'],
                'mobile' => $row['mobile'],
                'address' => $row['address'],
                'gender' => $row['gender'],
                'date_of_birth' => $dateOfBirth,
                'marital_status' => $row['marital_status'],
                'blood_group' => $row['blood_group'],
                'religion' => $row['religion'],
            ], [
                "name" => "required",
                'email' => 'required|email',
                'mobile' => 'required',
                'address' => 'required',
                'gender' => 'required',
                'date_of_birth' => 'required|date_format:d/m/Y',
                'marital_status' => 'required',
                'blood_group' => 'required',
                'religion' => 'required',
            ])->validate();
            $dateOfBirthFormatted = Carbon::createFromFormat('d/m/Y', $dateOfBirth)->format('Y-m-d');
            $employee = Employee::updateOrCreate(
                [
                    'email' => $row['email'],
                    'company_id' => $company_id,
                ],
                [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'company_id' => $company_id,
                    'mobile' => $row['mobile'],
                    'address' => $row['address'],
                    'gender' => $row['gender'],
                    'date_of_birth' => $dateOfBirthFormatted,
                    'marital_status' => $row['marital_status'],
                    'blood_group' => $row['blood_group'],
                    'religion' => $row['religion'],
                ]
            );
            return true;
        }
    }
}