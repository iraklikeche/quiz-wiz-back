<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage())
                    ->subject('Please verify your email')
                    ->greeting('Hello ' . $notifiable->name)
                    ->line('You’re almost there! To complete your sign up, please verify your email address.')
                    ->action('Verify now', $verificationUrl)
                    ->line('If you did not create an account, no further action is required.');
    }


    protected function verificationUrl($notifiable)
    {
        $expires = config('auth.verification.expire', 60);

        $tempUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($expires),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        $frontendUrl = 'http://127.0.0.1:5173/login';
        $queryString = parse_url($tempUrl, PHP_URL_QUERY);

        return $frontendUrl . '?' . $tempUrl;
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
