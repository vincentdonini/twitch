<?php

namespace App\Domain\Muscle\Entity;

use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MuscleRepository::class)]
#[ORM\Table(name: 'muscles')]
class Muscle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'muscle:list', 'muscle:detail',
    ])]
    #[OA\Property(description: "Muscle ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list', 'muscle:detail',
    ])]
    #[OA\Property(description: "Muscle slug", example: "upper-pectoralis")]
    private $slug;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'muscle:list', 'muscle:detail',
    ])]
    private MuscleArea $area;

    #[ORM\ManyToOne(targetEntity: MuscleGroup::class)]
    #[Groups([
        'muscle:list', 'muscle:detail',
    ])]
    private ?MuscleGroup $group = null;

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

    public function getGroup(): ?MuscleGroup
    {
        return $this->group;
    }

    public function setGroup(?MuscleGroup $group): self
    {
        $this->group = $group;
        return $this;
    }

}
