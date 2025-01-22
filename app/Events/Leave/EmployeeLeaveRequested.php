<?php

namespace App\Events\Leave;

use App\Helpers\AuthHelper;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeLeaveRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $employee_id;
    public $requested_to;
    public $employee_leave;
    public $company;

    public function __construct($employee_leave,$employee_id, $requested_to)
    {
        $this->employee_id = $employee_id;
        $this->requested_to = $requested_to;
        $this->employee_leave = $employee_leave;
        $this->company = AuthHelper::getCompanyInformation();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
