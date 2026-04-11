<?php

namespace App\Domain\Muscle\Entity;

use App\Domain\Content\Entity\ContentMuscleSegment;
use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Doctrine\Repository\Muscle\MuscleSegmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MuscleSegmentRepository::class)]
#[ORM\Table(name: 'muscle_segment')]
class MuscleSegment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "MuscleSegment ID")]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "MuscleSegment slug", example: "anterior-deltoid")]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Muscle::class, inversedBy: 'segments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Muscle $muscle;

    #[ORM\ManyToMany(targetEntity: Exercise::class, mappedBy: 'muscleSegments')]
    private Collection $exercises;

    #[ORM\OneToMany(targetEntity: ContentMuscleSegment::class, mappedBy: 'muscleSegment', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string $slug,
        Muscle $muscle,
    ) {
        $this->id     = Uuid::v7();
        $this->slug   = $slug;
        $this->muscle = $muscle;

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
    // MUSCLE (parent)
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscle(): Muscle
    {
        return $this->muscle;
    }

    public function setMuscle(Muscle $muscle): self
    {
        $this->muscle = $muscle;
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
            $exercise->addMuscleSegment($this);
        }
        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercises->removeElement($exercise)) {
            $exercise->removeMuscleSegment($this);
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

    public function addContent(ContentMuscleSegment $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscleSegment($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscleSegment $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscleSegment
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
