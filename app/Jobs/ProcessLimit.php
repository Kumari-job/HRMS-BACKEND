<?php

namespace App\Jobs;

use App\Mail\CreateLimitMail;
use App\Mail\ExcelExportMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ProcessLimit implements ShouldQueue
{
    use Queueable;

    public $remaining;
    public $type;
    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($remaining, $type, $user)
    {
        $this->remaining = $remaining;
        $this->type = $type;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new CreateLimitMail($this->type, $this->remaining));
    }
}
