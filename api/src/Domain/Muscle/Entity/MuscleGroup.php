<?php

namespace App\Domain\Muscle\Entity;

use App\Domain\Content\Entity\ContentMuscleGroup;
use App\Infrastructure\Doctrine\Repository\Muscle\MuscleGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: MuscleGroupRepository::class)]
#[ORM\Table(name: 'muscle_group')]
class MuscleGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "Muscle group ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Muscle group slug", example: "biceps")]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class, inversedBy: 'muscleGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private MuscleArea $muscleArea;

    #[ORM\OneToMany(targetEntity: Muscle::class, mappedBy: 'muscleGroup', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $muscles;

    #[ORM\OneToMany(targetEntity: ContentMuscleGroup::class, mappedBy: "muscleGroup", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
        $this->muscles  = new ArrayCollection();
        $this->contents = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): ?int
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
    // MUSCLE AREAS
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
            $muscle->setMuscleGroup($this);
        }
        return $this;
    }

    public function removeMuscle(Muscle $muscle): self
    {
        if ($this->muscles->removeElement($muscle)) {
            if ($muscle->getMuscleGroup() === $this) {
                $muscle->setMuscleGroup(null);
            }
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

    public function addContent(ContentMuscleGroup $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscleGroup($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscleGroup $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscleGroup
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
