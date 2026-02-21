<?php

namespace App\Domain\Muscle\Entity;

use App\Domain\Content\Entity\ContentMuscle;
use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Doctrine\Repository\Muscle\MuscleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MuscleRepository::class)]
#[ORM\Table(name: 'muscle')]
class Muscle
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Muscle ID")]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Muscle slug", example: "upper-pectoralis")]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MuscleArea $muscleArea;

    #[ORM\ManyToOne(targetEntity: MuscleGroup::class)]
    private ?MuscleGroup $muscleGroup = null;

    #[ORM\ManyToMany(targetEntity: Exercise::class, mappedBy: 'muscle')]
    private Collection $exercises;

    #[ORM\OneToMany(targetEntity: ContentMuscle::class, mappedBy: "muscle", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string     $slug,
        MuscleArea $muscleArea,
    ) {
        $this->id = Uuid::v7();

        $this->slug       = $slug;
        $this->muscleArea = $muscleArea;

        $this->exercises = new ArrayCollection();
        $this->contents  = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // MUSCLE AREA
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscleArea(): MuscleArea
    {
        return $this->muscleArea;
    }

    public function setMuscleArea(MuscleArea $muscleArea): self
    {
        $this->muscleArea = $muscleArea;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // MUSCLE GROUPS
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscleGroup(): ?MuscleGroup
    {
        return $this->muscleGroup;
    }

    public function setMuscleGroup(?MuscleGroup $muscleGroup): self
    {
        $this->muscleGroup = $muscleGroup;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // EXERCISES
    // -----------------------------------------------------------------------------------------------------------------
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(Exercise $exercise): self
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises->add($exercise);
            $exercise->addMuscle($this);
        }
        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercises->removeElement($exercise)) {
            $exercise->removeMuscle($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentMuscle $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscle($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscle $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscle
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
