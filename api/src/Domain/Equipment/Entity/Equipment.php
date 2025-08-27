<?php

namespace App\Domain\Equipment\Entity;

use App\Infrastructure\Persistence\Doctrine\Equipment\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: 'equipments')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[OA\Property(description: "Equipment ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[OA\Property(description: "Equipment name", example: "dip-bars")]
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
