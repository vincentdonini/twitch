<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Doctrine\Repository\Wod\WodVariantExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVariantExerciseRepository::class)]
#[ORM\Table(name: 'wod_variant_exercise')]
class WodVariantExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['wod:list', 'wod:detail', 'wodVariantExercise:list', 'wodVariantExercise:detail'])]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\ManyToOne(
        targetEntity: WodVariant::class,
        fetch: 'EAGER',
        inversedBy: 'wodVariantExercises'
    )]
    #[ORM\JoinColumn(nullable: false)]
    private WodVariant $wodVariant;

    #[ORM\ManyToOne(targetEntity: Exercise::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Exercise $exercise;

    #[ORM\OneToMany(
        targetEntity : WodVariantExerciseMetric::class,
        mappedBy     : 'exercise',
        cascade      : ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $metrics;

    public function __construct()
    {
        $this->metrics = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): ?int
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

    // -----------------------------------------------------------------------------------------------------------------
    // WOD VERSION VARIANT
    // -----------------------------------------------------------------------------------------------------------------
    public function getWodVariant(): WodVariant
    {
        return $this->wodVariant;
    }

    public function setWodVariant(WodVariant $variant): self
    {
        $this->wodVariant = $variant;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // EXERCISE
    // -----------------------------------------------------------------------------------------------------------------
    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function setExercise(Exercise $exercise): self
    {
        $this->exercise = $exercise;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // ATTRIBUTES / METRICS
    // -----------------------------------------------------------------------------------------------------------------
    public function getMetrics(): Collection
    {
        return $this->metrics;
    }

    public function addMetric(WodVariantExerciseMetric $metric): self
    {
        if (!$this->metrics->contains($metric)) {
            $this->metrics[] = $metric;
            $metric->setExercise($this);
        }
        return $this;
    }

    public function removeMetric(WodVariantExerciseMetric $metric): self
    {
        if ($this->metrics->contains($metric)) {
            $this->metrics->removeElement($metric);
        }
        return $this;
    }
}
