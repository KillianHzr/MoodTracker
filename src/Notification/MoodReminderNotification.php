<?php
namespace App\Notification;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Message\EmailMessage;

class MoodReminderNotification extends Notification
{
    private $username;

    public function __construct(string $username)
    {
        parent::__construct('N’oubliez pas d’enregistrer votre humeur !');
        $this->username = $username;
    }

    public function asEmailMessage(RecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = new EmailMessage();
        $email->subject($this->getSubject())
            ->from('noreply@moodtracker.com')
            ->to($recipient->getEmail())
            ->htmlTemplate('emails/mood_reminder.html.twig')
            ->context([
                'username' => $this->username,
            ]);

        return $email;
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['email'];
    }
}
