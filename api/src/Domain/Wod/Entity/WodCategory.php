<?php

namespace App\Domain\Wod\Entity;

use App\Infrastructure\Persistence\Doctrine\Wod\WodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodCategoryRepository::class)]
#[ORM\Table(name: 'wod_categories')]
class WodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "WOD Category ID")]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "WOD category slug", example: "the-heroes")]
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
