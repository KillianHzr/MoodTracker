<?php

namespace App\EventSubscriber;

use App\Entity\MoodEntry;
use App\Form\MoodEntryType;
use App\Repository\MoodEntryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

class MoodPopupSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private MoodEntryRepository $moodEntryRepository;
    private Environment $twig;
    private FormFactoryInterface $formFactory;

    public function __construct(
        Security $security,
        MoodEntryRepository $moodEntryRepository,
        Environment $twig,
        FormFactoryInterface $formFactory
    ) {
        $this->security = $security;
        $this->moodEntryRepository = $moodEntryRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        $showMoodPopup = false;
        $moodFormView = null;

        if ($user) {
            $today = new DateTime();
            $hasMoodToday = $this->moodEntryRepository->findOneBy([
                    'user' => $user,
                    'date' => $today,
                ]) !== null;

            $showMoodPopup = !$hasMoodToday;

            if ($showMoodPopup) {
                $moodEntry = new MoodEntry();
                $moodEntry->setDate($today);
                $moodEntry->setUser($user);
                $moodForm = $this->formFactory->create(MoodEntryType::class, $moodEntry);
                $moodFormView = $moodForm->createView();
            }
        }

        $this->twig->addGlobal('showMoodPopup', $showMoodPopup);
        $this->twig->addGlobal('moodForm', $moodFormView);
    }
}
