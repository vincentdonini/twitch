<?php

namespace App\Domain\Exercise\Entity;

use App\Domain\Equipment\Entity\Equipment;
use App\Infrastructure\Persistence\Doctrine\Exercise\ExerciseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\Table(name: 'exercises')]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Equipment::class)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(targetEntity: ExerciseCategory::class)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ExerciseCategory $exerciseCategory;

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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getExerciseCategory(): ExerciseCategory
    {
        return $this->exerciseCategory;
    }

    public function setExerciseCategory(ExerciseCategory $exerciseCategory): self
    {
        $this->exerciseCategory = $exerciseCategory;
        return $this;
    }
}