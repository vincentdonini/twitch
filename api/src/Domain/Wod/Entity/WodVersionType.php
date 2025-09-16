<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodVersionTypeProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: WodVersionTypeRepository::class)]
#[ORM\Table(name: 'wod_version_types')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
)]
#[GetCollection(
    normalizationContext: [
        'groups' => [
            'wodVersionType:list',
        ],
    ],
    provider            : WodVersionTypeProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wodVersionType:detail',
        ],
    ],
    provider            : WodVersionTypeProvider::class,
)]
class WodVersionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[OA\Property(description: "WOD version type ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[OA\Property(description: "WOD version type slug", example: "rx")]
    private string $slug;

    #[Groups([
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[SerializedName('description')]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: WodVersion::class, mappedBy: 'wodVersionType', cascade: ['persist', 'remove'])]
    private Collection $wodVersions;

    public function __construct()
    {
        $this->wodVersions = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, WodVersion>
     */
    public function getWodVersions(): Collection
    {
        return $this->wodVersions;
    }

    public function addWodVersion(WodVersion $wodVersion): self
    {
        if (!$this->wodVersions->contains($wodVersion)) {
            $this->wodVersions[] = $wodVersion;
            $wodVersion->setWodVersionType($this);
        }
        return $this;
    }

    public function removeWodVersion(WodVersion $wodVersion): self
    {
        if ($this->wodVersions->removeElement($wodVersion)) {
            if ($wodVersion->getWodVersionType() === $this) {
                $wodVersion->setWodVersionType(null);
            }
        }
        return $this;
    }
}
