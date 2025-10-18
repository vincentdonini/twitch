<?php

namespace App\Domain\Content\Entity;

use App\Domain\Muscle\Entity\MuscleArea;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ContentMuscleArea
{
    #[ORM\Id, ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class, inversedBy: "contents")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private MuscleArea $muscleArea;

    #[ORM\Column(type: "string", length: 5)]
    private string $locale;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $summary;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $details = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMuscleArea(): MuscleArea
    {
        return $this->muscleArea;
    }

    public function setMuscleArea(MuscleArea $muscleArea): self
    {
        $this->muscleArea = $muscleArea;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }
}
