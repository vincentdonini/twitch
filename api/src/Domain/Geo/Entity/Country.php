<?php

namespace App\Domain\Geo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'country')]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Country ID")]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $name;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $defaultLocale;

    #[ORM\OneToMany(targetEntity: Region::class, mappedBy: 'country')]
    private Collection $regions;

    public function __construct(
        string $slug,
        string $name,
        string $defaultLocale
    ) {
        $this->id = Uuid::v7();

        $this->slug          = $slug;
        $this->name          = $name;
        $this->defaultLocale = $defaultLocale;

        $this->regions = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(string $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // REGIONS
    // -----------------------------------------------------------------------------------------------------------------
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    public function addRegion(Region $region): self
    {
        if (!$this->regions->contains($region)) {
            $this->regions->add($region);
        }
        return $this;
    }

    public function removeRegion(Region $region): self
    {
        $this->regions->removeElement($region);
        return $this;
    }
}
