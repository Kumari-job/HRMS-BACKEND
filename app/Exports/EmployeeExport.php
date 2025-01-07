<?php

namespace App\Exports;


use App\Helpers\DateHelper;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $employees;
    private $headers = ["S.N.", "Name","Email","Mobile","Address","Date of Birth","Date of Birth Nepali","Marital Status","Blood Group","Religion"];

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function array(): array
    {
        $data = [];
        $counter = 1;
        foreach ($this->employees as $employee) {

            $excel_rows = [
                $counter++,
                $employee->name,
                $employee->email,
                $employee->mobile,
                $employee->address,
                $employee->date_of_birth,
                DateHelper::englishToNepali($employee->date_of_birth,'Y-m-d'),
                $employee->marital_status,
                $employee->blood_group,
                $employee->religion,
            ];
            array_push($data, [array_combine($this->headers, $excel_rows)]);
        }
        return $data;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 25,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
        ];
    }

}
