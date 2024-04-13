<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $token;
    public $email;


    public function __construct($token, $email)
    {
        //
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

        $url = $this->resetUrl($this->token, $this->email);

        return (new MailMessage())->view('email', ['url' => $url, 'user' => $notifiable->username,
                 'headerText' => 'Reset Your Password',
                 'text' => "We received a request to reset your password for your account. If you did not make this     request, please ignore this email. Otherwise, you can reset your password using the button below.",
                'buttonText' => 'Reset Password'])->subject('Please verify your email')->from('no-reply@quizwiz.com');
    }


    protected function resetUrl($token, $email)
    {
        return config('app.frontend_url')."/reset?token={$token}&email={$email}";
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
