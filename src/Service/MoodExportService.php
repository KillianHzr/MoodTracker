<?php

namespace App\Service;

use App\Repository\MoodEntryRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MoodExportService
{
    private MoodEntryRepository $moodEntryRepository;

    public function __construct(MoodEntryRepository $moodEntryRepository)
    {
        $this->moodEntryRepository = $moodEntryRepository;
    }

    public function exportUserMoodDataToCSV(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): StreamedResponse
    {
        $moodEntries = $this->moodEntryRepository->findMoodEntriesForUserByDateRange($user, $startDate, $endDate);

        $response = new StreamedResponse(function () use ($moodEntries) {
            $handle = fopen('php://output', 'w+');

            fputcsv($handle, ['Date', 'Mood', 'Note'], ';');

            foreach ($moodEntries as $entry) {
                fputcsv($handle, [
                    $entry->getDate()->format('Y-m-d'),
                    $entry->getMood(),
                    $entry->getNote(),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="mood_data.csv"');

        return $response;
    }
}
