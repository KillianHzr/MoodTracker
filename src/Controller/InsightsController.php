<?php

namespace App\Controller;

use App\Repository\MoodEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InsightsController extends AbstractController
{
    #[Route('/insights', name: 'app_insights')]
    public function index()
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->render('mood/login_prompt.html.twig', [
                'message' => 'You must be logged in to view the your Insights.',
            ]);
        }

        return $this->render('insights/index.html.twig');
    }

    #[Route('/insights/data', name: 'app_insights_data', methods: ['GET'])]
    public function data(Request $request, MoodEntryRepository $moodEntryRepository): JsonResponse
    {
        $user = $this->getUser();
        $period = $request->query->get('period', 'month');

        $startDate = match ($period) {
            'week' => (new \DateTime())->modify('-1 week'),
            'month' => (new \DateTime())->modify('-1 month'),
            'quarter' => (new \DateTime())->modify('-3 months'),
            'semester' => (new \DateTime())->modify('-6 months'),
            'year' => (new \DateTime())->modify('-1 year'),
            default => (new \DateTime())->modify('-10 years'),
        };

        $moodEntries = $moodEntryRepository->findMoodEntriesForUserByDateRange($user, $startDate);

        $dates = [];
        $moods = [];
        $moodFrequency = array_fill(1, 5, 0);

        foreach ($moodEntries as $entry) {
            $dates[] = $entry->getDate()->format('Y-m-d');
            $moods[] = $entry->getMood();
            $moodFrequency[$entry->getMood()]++;
        }

        return new JsonResponse([
            'dates' => $dates,
            'moods' => $moods,
            'moodFrequency' => array_values($moodFrequency),
        ]);
    }
}
