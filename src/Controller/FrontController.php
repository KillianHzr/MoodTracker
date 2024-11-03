<?php

namespace App\Controller;

use App\Entity\MoodEntry;
use App\Form\MoodEntryType;
use App\Repository\MoodEntryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(MoodEntryRepository $moodEntryRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $today = new DateTime();
        $hasMoodToday = false;

        if ($user) {
            $hasMoodToday = $moodEntryRepository->findOneBy([
                    'user' => $user,
                    'date' => $today,
                ]) !== null;
        }

        $moodEntry = new MoodEntry();
        $moodEntry->setDate($today);
        $moodEntry->setUser($user);
        $moodForm = $this->createForm(MoodEntryType::class, $moodEntry, [
            'action' => $this->generateUrl('app_mood_new'),
        ]);

        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'showMoodPopup' => !$hasMoodToday,
            'moodForm' => $moodForm->createView(),
        ]);
    }
}
