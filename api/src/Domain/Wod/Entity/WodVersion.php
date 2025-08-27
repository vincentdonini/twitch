<?php

namespace App\Domain\Wod\Entity;

use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVersionRepository::class)]
#[ORM\Table(name: 'wod_versions')]
class WodVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Wod::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private Wod $wod;

    #[ORM\ManyToOne(targetEntity: WodVersionType::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private WodVersionType $wodVersionType;

    #[ORM\OneToMany(targetEntity: WodVersionVariant::class, mappedBy: 'wodVersion', cascade: ['persist', 'remove'])]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private Collection $wodVersionVariants;

    public function __construct()
    {
        $this->wodVersionVariants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWod(): Wod
    {
        return $this->wod;
    }

    public function setWod(Wod $wod): self
    {
        $this->wod = $wod;
        return $this;
    }

    public function getWodVersionType(): WodVersionType
    {
        return $this->wodVersionType;
    }

    public function setWodVersionType(WodVersionType $wodVersionType): self
    {
        $this->wodVersionType = $wodVersionType;
        return $this;
    }

    /**
     * @return Collection<int, WodVersionVariant>
     */
    public function getWodVersionVariants(): Collection
    {
        return $this->wodVersionVariants;
    }

    public function addWodVersionVariant(WodVersionVariant $wodVersionVariant): self
    {
        if (!$this->wodVersionVariants->contains($wodVersionVariant)) {
            $this->wodVersionVariants[] = $wodVersionVariant;
            $wodVersionVariant->setWodVersion($this);
        }
        return $this;
    }

    public function removeWodVersion(WodVersionVariant $wodVersionVariant): self
    {
        if ($this->wodVersionVariants->removeElement($wodVersionVariant)) {
            if ($wodVersionVariant->getWodVersion() === $this) {
                $wodVersionVariant->setWodVersion(null);
            }
        }
        return $this;
    }

    public function setWodVersionVariants(iterable $wodVersionVariants): void
    {
        $this->wodVersionVariants = new ArrayCollection();

        foreach ($wodVersionVariants as $wodVersionVariant) {
            $this->addWodVersionVariant($wodVersionVariant);
        }
    }
}
