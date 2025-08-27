<?php

namespace App\Domain\Muscle\Entity;

use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleAreaRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MuscleAreaRepository::class)]
#[ORM\Table(name: 'muscle_areas')]
class MuscleArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail'
    ])]
    #[OA\Property(description: "Muscle area ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail'
    ])]
    #[OA\Property(description: "Muscle area slug", example: "arms")]
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
