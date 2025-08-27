<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionVariantExerciseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVersionVariantExerciseRepository::class)]
#[ORM\Table(name: 'wod_version_variant_exercises')]
class WodVersionVariantExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private int $position;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $reps = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $weight = null;

    #[ORM\ManyToOne(targetEntity: WodVersionVariant::class, inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private WodVersionVariant $wodVersionVariant;

    #[ORM\ManyToOne(targetEntity: Exercise::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private Exercise $exercise;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getWodVersionVariant(): WodVersionVariant
    {
        return $this->wodVersionVariant;
    }

    public function setWodVersionVariant(WodVersionVariant $variant): self
    {
        $this->wodVersionVariant = $variant;
        return $this;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function setExercise(Exercise $exercise): self
    {
        $this->exercise = $exercise;
        return $this;
    }

    public function getReps(): ?int
    {
        return $this->reps;
    }

    public function setReps(?int $reps): self
    {
        $this->reps = $reps;
        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
}
