<?php

namespace App\Controller;

use App\Entity\MoodEntry;
use App\Form\MoodEntryType;
use App\Repository\AdviceRepository;
use App\Repository\MoodEntryRepository;
use App\Service\MoodExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class MoodController extends AbstractController
{
    #[Route('/mood/new', name: 'app_mood_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AdviceRepository $adviceRepository,
        MailerInterface $mailer
    ): RedirectResponse|Response
    {
        $moodEntry = new MoodEntry();
        $form = $this->createForm(MoodEntryType::class, $moodEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moodEntry->setUser($this->getUser());
            $moodEntry->setDate(new DateTime());
            $entityManager->persist($moodEntry);
            $entityManager->flush();

            $advice = $adviceRepository->findOneBy(['moodLevel' => $moodEntry->getMood()]);

            if ($advice) {
                $email = (new Email())
                    ->from('noreply@moodtracker.com')
                    ->to($this->getUser()->getEmail())
                    ->subject('Votre conseil du jour')
                    ->html($this->renderView('emails/' . $advice->getEmailTemplate(), [
                        'user' => $this->getUser(),
                        'mood' => $moodEntry->getMood(),
                        'note' => $moodEntry->getNote(),
                    ]));
                $mailer->send($email);
            }

            return $this->redirectToRoute('app_mood_calendar_default');
        }

        return $this->render('mood/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/calendar', name: 'app_mood_calendar_default')]
    public function calendarDefault(): RedirectResponse
    {
        $currentDate = new DateTime();
        return $this->redirectToRoute('app_mood_calendar', [
            'year' => $currentDate->format('Y'),
            'month' => $currentDate->format('m'),
        ]);
    }

    #[Route('/calendar/{year}/{month}', name: 'app_mood_calendar', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function calendar(
        int $year,
        int $month,
        MoodEntryRepository $moodEntryRepository
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->render('mood/login_prompt.html.twig', [
                'message' => 'You must be logged in to view the Mood Calendar.',
            ]);
        }

        $startDate = new DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        $moodEntries = $moodEntryRepository->findByUserAndDateRange($user, $startDate, $endDate);

        $previousMonth = (clone $startDate)->modify('-1 month');
        $nextMonth = (clone $startDate)->modify('+1 month');

        return $this->render('mood/calendar.html.twig', [
            'moodEntries' => $moodEntries,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }

    #[Route('/export-csv', name: 'export_csv')]
    public function exportCSV(MoodExportService $moodExportService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $startDate = new \DateTime('-1 month');
        $endDate = new \DateTime('now');

        return $moodExportService->exportUserMoodDataToCSV($user, $startDate, $endDate);
    }
}
