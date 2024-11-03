<?php

namespace App\Controller;

use App\Repository\MoodEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }

    #[Route('/admin/insights/data', name: 'admin_insights_data', methods: ['GET'])]
    public function data(Request $request, MoodEntryRepository $moodEntryRepository): JsonResponse
    {
        $period = $request->query->get('period', 'month');

        $startDate = match ($period) {
            'week' => (new \DateTime())->modify('-1 week'),
            'month' => (new \DateTime())->modify('-1 month'),
            'quarter' => (new \DateTime())->modify('-3 months'),
            'semester' => (new \DateTime())->modify('-6 months'),
            'year' => (new \DateTime())->modify('-1 year'),
            default => (new \DateTime())->modify('-10 years'),
        };

        $moodEntries = $moodEntryRepository->findMoodEntriesByDateRange($startDate);

        $dates = [];
        $moods = [];
        $moodFrequency = array_fill(1, 5, 0);

        foreach ($moodEntries as $entry) {
            $dates[] = $entry->getDate()->format('Y-m-d');
            $moods[] = $entry->getMood();
            $moodFrequency[$entry->getMood()]++;
        }

        return new JsonResponse([
            'dates' => array_unique($dates),
            'moods' => $moods,
            'moodFrequency' => array_values($moodFrequency),
        ]);
    }
}
