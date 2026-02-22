<?php

namespace App\Domain\Wod\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'wod_variant_exercise_metric')]
class WodVariantExerciseMetric
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: WodVariantExercise::class, inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private WodVariantExercise $exercise;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'float')]
    private float $value;

    public function __construct(
        WodVariantExercise $exercise,
        string             $type,
        float              $value,
    ) {
        $this->id = Uuid::v7();

        $this->exercise = $exercise;
        $this->type     = $type;
        $this->value    = $value;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
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
