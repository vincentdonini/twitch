<?php

namespace App\Domain\Wod\Entity;

use App\Infrastructure\Persistence\Doctrine\Wod\WodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodRepository::class)]
#[ORM\Table(name: 'wods')]
class Wod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail'
    ])]
    private ?int $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail'
    ])]
    private string $title;

    #[ORM\Column(type: Types::JSON)]
    #[Groups([
        'wod:list', 'wod:detail'
    ])]
    private array $description = [];

    #[ORM\ManyToOne(targetEntity: WodType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail'
    ])]
    private WodType $wodType;

    #[ORM\ManyToOne(targetEntity: WodCategory::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail'
    ])]
    private WodCategory $wodCategory;

    #[ORM\OneToMany(mappedBy: 'wod', targetEntity: WodVersion::class, cascade: ['persist', 'remove'])]
    #[Groups([
        'wod:list', 'wod:detail'
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

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
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
