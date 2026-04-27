<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));

        return (new MailMessage)
            ->subject('Réinitialiser ton mot de passe PIXELZONE')
            ->greeting('Salut ' . $notifiable->pseudo . ' !')
            ->line('Tu as demandé une réinitialisation de mot de passe.')
            ->action('Réinitialiser mon mot de passe', $url)
            ->line('Ce lien expire dans 60 minutes.')
            ->line('Si tu n\'as pas demandé cette réinitialisation, ignore cet email.')
            ->salutation('Bonne chance pour ton oral ! 🎮');
    }
}
