<?php

namespace App\Listeners;

use App\Events\Leave\EmployeeLeaveRequested;
use App\Helpers\AuthHelper;
use App\Models\Department;
use App\Models\User;
use App\Notifications\LeaveNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendLeaveNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeLeaveRequested $event): void
    {
        $employee_id = $event->employee_id;
        $requested_to = $event->requested_to;
        $employee_leave = $event->employee_leave;
        $company_name = $event->company['name'];
        foreach ($requested_to as $requested) {
            $user = User::find($requested);
            Log::info($user);
            $user->notify(new LeaveNotification($employee_leave,$company_name));
        }
    }
}
