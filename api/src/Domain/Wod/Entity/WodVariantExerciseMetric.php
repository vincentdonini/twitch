<?php

namespace App\Domain\Wod\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'wod_variant_exercise_metric')]
class WodVariantExerciseMetric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: WodVariantExercise::class, inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private WodVariantExercise $exercise;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'float')]
    private float $value;

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExercise(): WodVariantExercise
    {
        return $this->exercise;
    }

    public function setExercise(WodVariantExercise $exercise): self
    {
        $this->exercise = $exercise;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }
}
