<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: WodRepository::class)]
#[ORM\Table(name: 'wods')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title'                                 => 'partial',
        'description'                           => 'partial',
        'wodType'                               => 'exact',
        'wodType.slug'                          => 'exact',
        'wodCategory'                           => 'exact',
        'wodCategory.slug'                      => 'exact',
        'wodVersions.wodVersionVariants.gender' => 'exact',
    ]
)]
//#[ApiFilter(
//    RangeFilter::class,
//    properties: [
//        'wodVersions.wodVersionVariants.timeCap',
//        'wodVersions.wodVersionVariants.rounds',
//    ]
//)]
//#[ApiFilter(
//    OrderFilter::class,
//    properties: [
//        'id',
//        'title',
//    ],
//    arguments : [
//        'orderParameterName' => 'order',
//    ]
//)]
#[GetCollection(
    normalizationContext: [
        'groups' => [
            'wod:list',
        ],
    ],
    provider            : WodProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wod:detail',
        ],
    ],
    provider            : WodProvider::class,
)]
class Wod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private string $title;

    #[ORM\Column(type: Types::JSON)]
    private array $descriptions = [];

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[SerializedName('description')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: WodType::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private WodType $wodType;

    #[ORM\ManyToOne(targetEntity: WodCategory::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private WodCategory $wodCategory;

    #[ORM\OneToMany(targetEntity: WodVersion::class, mappedBy: 'wod', cascade: ['persist', 'remove'])]
    #[ApiProperty(fetchEager: true)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private Collection $wodVersions;

    public function __construct()
    {
        $this->wodVersions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function setDescriptions(array $descriptions): self
    {
        $this->descriptions = $descriptions;
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

    public function getWodType(): WodType
    {
        return $this->wodType;
    }

    public function setWodType(WodType $wodType): self
    {
        $this->wodType = $wodType;
        return $this;
    }

    public function getWodCategory(): WodCategory
    {
        return $this->wodCategory;
    }

    public function setWodCategory(WodCategory $wodCategory): self
    {
        $this->wodCategory = $wodCategory;
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
            $wodVersion->setWod($this);
        }
        return $this;
    }

    public function removeWodVersion(WodVersion $wodVersion): self
    {
        if ($this->wodVersions->removeElement($wodVersion)) {
            if ($wodVersion->getWod() === $this) {
                $wodVersion->setWod(null);
            }
        }
        return $this;
    }
}
