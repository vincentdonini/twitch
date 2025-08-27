<?php

namespace App\Domain\Wod\Entity;

use App\Infrastructure\Persistence\Doctrine\Wod\WodTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodTypeRepository::class)]
#[ORM\Table(name: 'wod_types')]
class WodType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "WOD Type ID")]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "WOD type slug", example: "for-load")]
    private string $slug;

    public function getId(): int
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
