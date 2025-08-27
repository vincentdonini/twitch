<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Wod\Enum\Gender;
use Symfony\Component\Validator\Constraints as Assert;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionVariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVersionVariantRepository::class)]
#[ORM\Table(name: 'wod_version_variants')]
class WodVersionVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: WodVersion::class, inversedBy: 'versionVariants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private WodVersion $wodVersion;

    #[ORM\Column(type: 'string', length: 10, enumType: Gender::class)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    #[Assert\Choice(callback: [Gender::class, 'cases'])]
    private Gender $gender;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $rounds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private ?int $timeCap = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private array $description = [];

    #[ORM\OneToMany(targetEntity: WodVersionVariantExercise::class, mappedBy: 'wodVersionVariant', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    private Collection $wodVersionVariantExercises;

    public function __construct()
    {
        $this->wodVersionVariantExercises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWodVersion(): WodVersion
    {
        return $this->wodVersion;
    }

    public function setWodVersion(WodVersion $wodVersion): self
    {
        $this->wodVersion = $wodVersion;
        return $this;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getRounds(): ?int
    {
        return $this->rounds;
    }

    public function setRounds(?int $rounds): self
    {
        $this->rounds = $rounds;
        return $this;
    }

    public function getTimeCap(): ?int
    {
        return $this->timeCap;
    }

    public function setTimeCap(?int $timeCap): self
    {
        $this->timeCap = $timeCap;
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

    /**
     * @return Collection<int, WodVersionVariantExercise>
     */
    public function getWodVersionVariantExercises(): Collection
    {
        return $this->wodVersionVariantExercises;
    }

    public function addWodVersionVariantExercise(WodVersionVariantExercise $wodVersionVariantExercise): self
    {
        if (!$this->wodVersionVariantExercises->contains($wodVersionVariantExercise)) {
            $this->wodVersionVariantExercises[] = $wodVersionVariantExercise;
            $wodVersionVariantExercise->setWodVersionVariant($this);
        }
        return $this;
    }

    public function removeWodVersionVariantExercise(WodVersionVariantExercise $wodVersionVariantExercise): self
    {
        if ($this->wodVersionVariantExercises->removeElement($wodVersionVariantExercise)) {
            if ($wodVersionVariantExercise->getWodVersionVariant() === $this) {
                $wodVersionVariantExercise->setWodVersionVariant(null);
            }
        }
        return $this;
    }
}
