<?php

namespace App\Domain\Exercise\Entity;

use App\Infrastructure\Persistence\Doctrine\Exercise\ExerciseCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExerciseCategoryRepository::class)]
#[ORM\Table(name: 'exercise_categories')]
class ExerciseCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['exercise:list', 'exercise:detail', 'exerciseCategory:list', 'exerciseCategory:detail'])]
    #[OA\Property(description: "Exercise category ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['exerciseCategory:list', 'exerciseCategory:detail'])]
    #[OA\Property(description: "Exercise category slug", example: "warm-up")]
    private $slug;

    public function __construct()
    {

    }

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
}
