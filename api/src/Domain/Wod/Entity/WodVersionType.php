<?php

namespace App\Domain\Wod\Entity;

use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVersionTypeRepository::class)]
#[ORM\Table(name: 'wod_version_types')]
class WodVersionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersion:list', 'wodVersion:detail',
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[OA\Property(description: "WOD version type ID")]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersion:list', 'wodVersion:detail',
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    #[OA\Property(description: "WOD version type slug", example: "rx")]
    private string $slug;

    #[ORM\OneToMany(mappedBy: 'wodVersionType', targetEntity: WodVersion::class)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersion:list', 'wodVersion:detail',
        'wodVersionType:list', 'wodVersionType:detail',
    ])]
    private Collection $wodVersions;

    public function __construct()
    {
        $this->wodVersions = new ArrayCollection();
    }

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
        if ($this->wodVersions->contains($wodVersion)) {
            $this->wodVersions->removeElement($wodVersion);
            // set the owning side to null (unless already changed)
            if ($wodVersion->getWodVersionType() === $this) {
                $wodVersion->setWodVersionType(null);
            }
        }

        return $this;
    }
}
