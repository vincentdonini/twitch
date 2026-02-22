<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Doctrine\Repository\Wod\WodVariantExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WodVariantExerciseRepository::class)]
#[ORM\Table(name: 'wod_variant_exercise')]
class WodVariantExercise
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\ManyToOne(
        targetEntity: WodVariant::class,
        fetch       : 'EAGER',
        inversedBy  : 'wodVariantExercises'
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

    public function __construct(
        int        $position,
        WodVariant $wodVariant,
        Exercise   $exercise
    ) {
        $this->id = Uuid::v7();

        $this->position   = $position;
        $this->wodVariant = $wodVariant;
        $this->exercise   = $exercise;

        $this->metrics = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
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
