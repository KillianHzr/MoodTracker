<?php

namespace App\Controller;

use App\Service\MoodReminderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/send-reminder', name: 'send_reminder')]
    public function sendReminder(MoodReminderService $moodReminderService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $moodReminderService->sendMoodReminder($user);

        return new Response('Notification envoyée !');
    }
}
