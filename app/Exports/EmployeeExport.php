<?php

namespace App\Exports;


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

    protected $batches;
    private $headers = ["S.N.", "Name","Start date",'End date',"Shifts","Instructor","Total Admissions","Collected Amount"];

    public function __construct($batches)
    {
        $this->batches = $batches;
    }

    public function array(): array
    {
        $data = [];
        $counter = 1;
        foreach ($this->batches as $batch) {
            $instructor_names = [];
            $shifts_info = [];
            $collected_amount = 0;
            $collected_amount = SalesHelper::collectedAmount($batch);
            $total_admissions = $batch->admissions->count();
            foreach($batch->instructors as $instructor)
            {
                $instructor_names[] = $instructor->name ?? null;
            }
            foreach($batch->shifts as $shift)
            {
                $start_time = Carbon::parse($shift->start_time)->format('h:i A') ?? null;
                $end_time = Carbon::parse($shift->end_time)->format('h:i A') ?? null;
                $shifts_info[] = "{$shift->title} ({$start_time} - {$end_time})";
            }
            $instructor_names_string = implode(',', $instructor_names);
            $shifts_info_string = implode(',', $shifts_info);
            $excel_rows = [
                $counter++,
                "(".$batch->title.")". $batch->course->title,
                $batch->start_date,
                $batch->end_date,
                $shifts_info_string,
                $instructor_names_string,
                $total_admissions ?? 0,
                $collected_amount ?? 0
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
            'B' => 40,
            'C' => 15,
            'D' => 15,
            'E' => 25,
            'F' => 15,
            'G' => 15
        ];
    }

}
