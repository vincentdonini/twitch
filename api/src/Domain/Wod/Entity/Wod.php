<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Content\Entity\ContentWod;
use App\Infrastructure\Doctrine\Repository\Wod\WodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WodRepository::class)]
#[ORM\Table(name: 'wod')]
class Wod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: WodType::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private WodType $wodType;

    #[ORM\ManyToOne(targetEntity: WodCategory::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private WodCategory $wodCategory;

    #[ORM\OneToMany(
        targetEntity: WodVariant::class,
        mappedBy    : 'wod',
        cascade     : ['persist', 'remove'])
    ]
    private Collection $wodVariants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $teamSize = null;

    #[ORM\OneToMany(targetEntity: ContentWod::class, mappedBy: "wod", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string      $name,
        WodType     $wodType,
        WodCategory $wodCategory,
    ) {
        $this->name       = $name;
        $this->wodType     = $wodType;
        $this->wodCategory = $wodCategory;
        $this->wodVariants = new ArrayCollection();
        $this->contents    = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    // -----------------------------------------------------------------------------------------------------------------
    // VARIANTS
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return Collection<int, WodVariant>
     */
    public function getWodVariants(): Collection
    {
        return $this->wodVariants;
    }

    public function addWodVariant(WodVariant $wodVariant): self
    {
        if (!$this->wodVariants->contains($wodVariant)) {
            $this->wodVariants[] = $wodVariant;
            $wodVariant->setWod($this);
        }
        return $this;
    }

    public function getTeamSize(): ?int
    {
        return $this->teamSize;
    }

    public function setTeamSize(?int $teamSize): self
    {
        $this->teamSize = $teamSize;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentWod $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setWod($this);
        }
        return $this;
    }

    public function removeContent(ContentWod $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentWod
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
