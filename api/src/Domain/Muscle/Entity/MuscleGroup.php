<?php

namespace App\Domain\Muscle\Entity;

use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MuscleGroupRepository::class)]
#[ORM\Table(name: 'muscle_groups')]
class MuscleGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    #[OA\Property(description: "Muscle group ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    #[OA\Property(description: "Muscle group slug", example: "biceps")]
    private $slug;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class, inversedBy: 'areas')]
    #[ORM\JoinColumn(nullable: false)]
    private $area;

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

    public function getArea(): MuscleArea
    {
        return $this->area;
    }

    public function setArea(MuscleArea $area): self
    {
        $this->area = $area;
        return $this;
    }
}
