<?php

namespace App\Domain\Organization\Entity;

use App\Domain\Geo\Entity\City;
use App\Domain\Organization\Enum\PlaceStatusEnum;
use App\Domain\User\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'place')]
class Place
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Place ID")]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'places')]
    private Company $company;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $legalName;

    #[ORM\Column(type: 'string', length: 14, unique: true, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address2 = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $postalCode;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private City $city;

    #[ORM\Column(type: 'smallint')]
    private int $nbDaysBeforeReservation = 0;

    #[ORM\Column(type: 'smallint')]
    private int $nbHoursBeforeCancelReservation = 0;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $registrationDate = null;

    #[ORM\Column(type: 'string', enumType: PlaceStatusEnum::class)]
    private PlaceStatusEnum $status = PlaceStatusEnum::ACTIVE;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'places')]
    private Collection $users;

    public function __construct(
        Company $company,
        string  $slug,
        string  $name,
        string  $legalName,
        string  $address,
        string  $postalCode,
        City    $city,
    ) {
        $this->id         = Uuid::v7();
        $this->company    = $company;
        $this->slug       = $slug;
        $this->name       = $name;
        $this->legalName  = $legalName;
        $this->address    = $address;
        $this->postalCode = $postalCode;
        $this->city       = $city;
        $this->createdAt  = new DateTimeImmutable();
        $this->users      = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;
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

    public function getNbDaysBeforeReservation(): int
    {
        return $this->nbDaysBeforeReservation;
    }

    public function setNbDaysBeforeReservation(int $value): self
    {
        $this->nbDaysBeforeReservation = $value;
        return $this;
    }

    public function getNbHoursBeforeCancelReservation(): int
    {
        return $this->nbHoursBeforeCancelReservation;
    }

    public function setNbHoursBeforeCancelReservation(int $value): self
    {
        $this->nbHoursBeforeCancelReservation = $value;
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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
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

    public function getStatus(): PlaceStatusEnum
    {
        return $this->status;
    }

    public function setStatus(PlaceStatusEnum $status): self
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
    // USERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addPlace($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removePlace($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // COMPANY
    // -----------------------------------------------------------------------------------------------------------------
    public function assignToCompany(Company $company): self
    {
        $this->company = $company;
        if (!$company->getPlaces()->contains($this)) {
            $company->addPlace($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function isOwner(User $user): bool
    {
        return $this->company->getOwners()->exists(
            fn($key, User $owner) => $owner->getId()->equals($user->getId())
        );
    }

    public function isCoach(User $user): bool
    {
        return $this->users->contains($user) && in_array('ROLE_COACH', $user->getRoles(), true);
    }
}
