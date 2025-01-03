<?php

namespace App\Jobs;

use App\Exports\EmployeeExport;
use App\Helpers\AuthHelper;
use App\Helpers\DirectoryPathHelper;
use App\Mail\ExcelExportMail;
use App\Models\Download;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProcessEmployeeExport
{
    use Queueable;

    public $title = "Employee Export";
    public $company_id;
    public $user;
    public $employees;

    /**
     * Create a new job instance.
     */
    public function __construct($employees, $company_id, $user)
    {
        $this->employees = $employees;
        $this->company_id = $company_id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyId = $this->company_id;

        $fileName = $this->title . "_" . $companyId . "_" . date("Y-m-d", strtotime(now())) . "_" . time() . ".xlsx";
        $path = DirectoryPathHelper::downloadDirectoryPath($companyId);

        $download = new Download();
        $download->company_id = $companyId;
        $download->filename = $fileName;
        $download->title = $this->title;
        $download->state = "processing";
        $download->created_by = Auth::id();
        $download->save();

        $export = new EmployeeExport($this->employees);
        $storeStatus = Excel::store($export, $path . '/' . $fileName, 'public');
        if ($storeStatus) {
            $download->state = "ready";
            if(isset($idpUser['email'])){
                Mail::to($idpUser['email'])->send(new ExcelExportMail('TMS Fee Payment Export','fee payment',$path . '/' . $fileName,$idpUser['name']),);
            }
        } else {
            $download->state = "failed";
        }
        $download->save();
    }
}
