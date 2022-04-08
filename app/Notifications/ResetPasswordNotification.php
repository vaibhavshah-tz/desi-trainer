<?php

namespace App\Notifications;

use CommonHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $emailTemplate, $user)
    {
        $this->token = $token;
        $this->emailTemplate = $emailTemplate;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $replaceArr = [
            'FULL_NAME' => $this->user->full_name,
            'URL' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)),
            'MINUTE' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
        ];
        $emailContent = CommonHelper::replaceEmailContents($this->emailTemplate->body, $replaceArr);

        return (new MailMessage)
                    ->subject($this->emailTemplate->subject)
                    ->view('emails.common-mail', compact('emailContent'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
