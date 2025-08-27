<?php

namespace App\Domain\Movement\Entity;

use App\Infrastructure\Persistence\Doctrine\Movement\MovementRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MovementRepository::class)]
#[ORM\Table(name: 'movements')]
class Movement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['movement:list', 'movement:detail'])]
    #[OA\Property(description: "Movement ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['movement:list', 'movement:detail'])]
    #[OA\Property(description: "Movement slug", example: "air-squat")]
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
