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

        $path = parse_url($tempUrl, PHP_URL_PATH);
        $pathComponents = explode('/', trim($path, '/'));

        $idIndex = array_search('verify', $pathComponents) + 1;
        $id = $pathComponents[$idIndex] ?? null;
        $hash = $pathComponents[$idIndex + 1] ?? null;

        return $frontendUrl . "?id={$id}&hash={$hash}&{$queryString}";

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


// http://127.0.0.1:5173/login?id=110&hash=b0354b4c4d17bb6fafc1f4a7fc3eacfe411de96b&expires=1711645656&signature=9569080d87193cb6f27a48606b915471922cbc83d6b489dc5e18082a3dd00d19

// http://127.0.0.1:5173/login?http://127.0.0.1:8000/api/email/verify/110/b0354b4c4d17bb6fafc1f4a7fc3eacfe411de96b?expires=1711645656&signature=9569080d87193cb6f27a48606b915471922cbc83d6b489dc5e18082a3dd00d19
