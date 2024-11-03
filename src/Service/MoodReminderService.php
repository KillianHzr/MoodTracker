<?php

namespace App\Service;

use App\Notification\MoodReminderNotification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use App\Entity\User;

class MoodReminderService
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function sendMoodReminder(User $user): void
    {
        $notification = new MoodReminderNotification($user->getUsername());
        $recipient = new Recipient($user->getEmail());

        $this->notifier->send($notification, $recipient);
    }
}
