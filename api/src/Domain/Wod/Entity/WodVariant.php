<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Wod\Enum\GenderEnum;
use App\Infrastructure\Doctrine\Repository\Wod\WodVariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVariantRepository::class)]
#[ORM\Table(name: 'wod_variant')]
class WodVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['wod:list', 'wod:detail'])]
    private ?int $id = null;

    // -----------------------------------------------------------------------------------------------------------------
    // WOD
    // -----------------------------------------------------------------------------------------------------------------
    #[ORM\ManyToOne(targetEntity: Wod::class, inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    private Wod $wod;

    // -----------------------------------------------------------------------------------------------------------------
    // DIVISION (rx, master, teen)
    // -----------------------------------------------------------------------------------------------------------------
    #[ORM\ManyToOne(targetEntity: WodDivision::class, inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['wod:list', 'wod:detail'])]
    private WodDivision $wodDivision;

    // -----------------------------------------------------------------------------------------------------------------
    // AGE RANGE
    // -----------------------------------------------------------------------------------------------------------------
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['wod:list', 'wod:detail'])]
    private ?WodAgeRange $wodAgeRange = null;

    // -----------------------------------------------------------------------------------------------------------------
    // GENDER
    // -----------------------------------------------------------------------------------------------------------------
    #[ORM\Column(type: 'string', length: 10, enumType: GenderEnum::class, nullable: true)]
    #[Groups(['wod:list', 'wod:detail'])]
    private ?GenderEnum $gender = null;

    // -----------------------------------------------------------------------------------------------------------------
    // SCALED / RX
    // -----------------------------------------------------------------------------------------------------------------
    #[ORM\Column(type: 'boolean')]
    #[Groups(['wod:list', 'wod:detail'])]
    private bool $isScaled = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rounds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $timeCap = null;

    #[ORM\OneToMany(
        targetEntity : WodVariantExercise::class,
        mappedBy     : 'wodVariant',
        cascade      : ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $wodVariantExercises;

    public function __construct()
    {
        $this->wodVariantExercises = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
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

    public function getWodDivision(): WodDivision
    {
        return $this->wodDivision;
    }

    public function setWodDivision(WodDivision $wodDivision): self
    {
        $this->wodDivision = $wodDivision;
        return $this;
    }

    public function getWodAgeRange(): ?WodAgeRange
    {
        return $this->wodAgeRange;
    }

    public function setWodAgeRange(?WodAgeRange $wodAgeRange): self
    {
        $this->wodAgeRange = $wodAgeRange;
        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(?GenderEnum $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function isScaled(): bool
    {
        return $this->isScaled;
    }

    public function setIsScaled(bool $isScaled): self
    {
        $this->isScaled = $isScaled;
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

    // -----------------------------------------------------------------------------------------------------------------
    // EXERCISES
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return Collection<int, WodVariantExercise>
     */
    public function getWodVariantExercises(): Collection
    {
        return $this->wodVariantExercises;
    }

    public function addWodVariantExercise(WodVariantExercise $exercise): self
    {
        if (!$this->wodVariantExercises->contains($exercise)) {
            $this->wodVariantExercises[] = $exercise;
            $exercise->setWodVariant($this);
        }
        return $this;
    }
}
