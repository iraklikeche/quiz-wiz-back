<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
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
                    ->greeting('Hello')
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

        $fullUrl = config('app.frontend_url') . '/login?verify_url=' . urlencode($tempUrl);
        // $fullUrl = config('app.frontend_url') . '/login?verify_url=' . http_build_query([
        // 'id' => $notifiable->getKey(),
        // 'hash' => sha1($notifiable->getEmailForVerification())
        // ]);

        return $fullUrl;

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
