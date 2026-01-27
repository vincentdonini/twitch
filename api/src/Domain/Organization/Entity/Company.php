<?php

namespace App\Domain\Organization\Entity;

use App\Domain\Geo\Entity\City;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Repository\Organization\CompanyRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'company')]
class Company
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Company ID")]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $legalName;

    #[ORM\Column(type: 'string', length: 9, unique: true, nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(type: 'string', length: 14, unique: true, nullable: true)]
    private ?string $vatNumber = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $legalForm = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $activityCode = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address2 = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $postalCode;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private City $city;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $registrationDate = null;

    #[ORM\Column(type: 'string', enumType: CompanyStatusEnum::class)]
    private CompanyStatusEnum $status = CompanyStatusEnum::ACTIVE;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'companies')]
    private Collection $owners;

    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'company', cascade: ['persist'], orphanRemoval: true)]
    private Collection $places;

    public function __construct(
        string $slug,
        string $name,
        string $legalName,
        string $address,
        string $postalCode,
        City   $city,
    ) {
        $this->id        = Uuid::v7();
        $this->slug      = $slug;
        $this->name      = $name;
        $this->legalName = $legalName;
        $this->address   = $address;
        $this->postalCode   = $postalCode;
        $this->city      = $city;
        $this->createdAt = new DateTimeImmutable();
        $this->owners    = new ArrayCollection();
        $this->places    = new ArrayCollection();
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

    public function getLegalName(): string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): self
    {
        $this->legalName = $legalName;
        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;
        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }

    public function getLegalForm(): ?string
    {
        return $this->legalForm;
    }

    public function setLegalForm(?string $legalForm): self
    {
        $this->legalForm = $legalForm;
        return $this;
    }

    public function getActivityCode(): ?string
    {
        return $this->activityCode;
    }

    public function setActivityCode(?string $activityCode): self
    {
        $this->activityCode = $activityCode;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;
        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRegistrationDate(): ?DateTimeImmutable
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?DateTimeImmutable $registrationDate): self
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    public function getStatus(): CompanyStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CompanyStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // OWNERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $user): self
    {
        if (!$this->owners->contains($user)) {
            $this->owners[] = $user;
            $user->addCompany($this);
        }
        return $this;
    }

    public function removeOwner(User $user): self
    {
        if ($this->owners->removeElement($user)) {
            $user->removeCompany($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PLACES
    // -----------------------------------------------------------------------------------------------------------------
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->assignToCompany($this);
        }
        return $this;
    }
}
