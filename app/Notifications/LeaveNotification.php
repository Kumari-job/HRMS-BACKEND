<?php

namespace App\Notifications;

use App\Models\CompanyLeave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveNotification extends Notification
{
    use Queueable;
    public $employee_leave;
    public $leave;
    public $user;
    public $company_name;

    /**
     * Create a new notification instance.
     */
    public function __construct($employee_leave, $company_name)
    {
        $this->employee_leave = $employee_leave;
        $this->company_name = $company_name;
        $this->user = User::find($this->employee_leave->requested_by);
        $this->leave = CompanyLeave::find($this->employee_leave->leave_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Leave Request')
                    ->markdown('mail.leave-request', [
                        'name' => $this->user->name,
                        'start_date' => $this->employee_leave->start_date,
                        'company_name' => $this->company_name,
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        return [
            'title' => 'Employee Leave',
            'description' => $this->user->name. " has requested ". $this->leave->name ." on " .$this->employee_leave->start_date,
        ];
    }
}
