<?php

namespace App\Domain\Exercise\Entity;

use App\Domain\Content\Entity\ContentExercise;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleSegment;
use App\Infrastructure\Doctrine\Repository\Exercise\ExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\Table(name: 'exercise')]
class Exercise
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Equipment::class, fetch: 'EAGER')]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(targetEntity: ExerciseCategory::class, fetch: 'EAGER')]
    private ExerciseCategory $exerciseCategory;

    #[ORM\ManyToMany(targetEntity: Muscle::class)]
    #[ORM\JoinTable(
        name              : 'exercise_muscle',
        joinColumns       : [new ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'muscle_id', referencedColumnName: 'id')]
    )]
    private Collection $muscles;

    #[ORM\ManyToMany(targetEntity: MuscleSegment::class)]
    #[ORM\JoinTable(
        name              : 'exercise_muscle_segment',
        joinColumns       : [new ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'muscle_segment_id', referencedColumnName: 'id')]
    )]
    private Collection $muscleSegments;

    #[ORM\OneToMany(targetEntity: ContentExercise::class, mappedBy: "exercise", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string           $slug,
        ExerciseCategory $exerciseCategory
    ) {
        $this->id = Uuid::v7();

        $this->slug             = $slug;
        $this->exerciseCategory = $exerciseCategory;

        $this->muscles         = new ArrayCollection();
        $this->muscleSegments  = new ArrayCollection();
        $this->contents        = new ArrayCollection();
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
    // EQUIPMENT
    // -----------------------------------------------------------------------------------------------------------------
    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // EXERCISE CATEGORY
    // -----------------------------------------------------------------------------------------------------------------
    public function getExerciseCategory(): ExerciseCategory
    {
        return $this->exerciseCategory;
    }

    public function setExerciseCategory(ExerciseCategory $exerciseCategory): self
    {
        $this->exerciseCategory = $exerciseCategory;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // MUSCLES
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscles(): Collection
    {
        return $this->muscles;
    }

    public function addMuscle(Muscle $muscle): self
    {
        if (!$this->muscles->contains($muscle)) {
            $this->muscles->add($muscle);
        }
        return $this;
    }

    public function removeMuscle(Muscle $muscle): self
    {
        $this->muscles->removeElement($muscle);
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // MUSCLE SEGMENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscleSegments(): Collection
    {
        return $this->muscleSegments;
    }

    public function addMuscleSegment(MuscleSegment $segment): self
    {
        if (!$this->muscleSegments->contains($segment)) {
            $this->muscleSegments->add($segment);
        }
        return $this;
    }

    public function removeMuscleSegment(MuscleSegment $segment): self
    {
        $this->muscleSegments->removeElement($segment);
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentExercise $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setExercise($this);
        }
        return $this;
    }

    public function removeContent(ContentExercise $content): self
    {
        $this->contents->removeElement($content);
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentExercise
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
