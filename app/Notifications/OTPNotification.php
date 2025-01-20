<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OTPNotification extends Notification
{
    use Queueable;

    public $otp;
    public $token;
    public $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($otp, $token, $email)
    {
        $this->otp = $otp;
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $data = [];
        $data['otp_code'] = $this->otp;
        $data['url'] = url(config('custom.client_app.hrms_app_frontend_url') .'/auth/verify-token/'.$this->token, false);
        return (new MailMessage)->subject('Reset your Password')->view('mail.forgot-password-email', $data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
