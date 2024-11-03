<?php

namespace App\Entity;

use App\Repository\AdviceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $moodLevel = null;

    #[ORM\Column(length: 255)]
    private ?string $emailTemplate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoodLevel(): ?int
    {
        return $this->moodLevel;
    }

    public function setMoodLevel(int $moodLevel): static
    {
        $this->moodLevel = $moodLevel;

        return $this;
    }

    public function getEmailTemplate(): ?string
    {
        return $this->emailTemplate;
    }

    public function setEmailTemplate(string $emailTemplate): static
    {
        $this->emailTemplate = $emailTemplate;

        return $this;
    }
}
