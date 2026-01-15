<?php

namespace App\Domain\Box\Entity;

use App\Infrastructure\Doctrine\Repository\Box\BoxRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BoxRepository::class)]
#[ORM\Table(name: 'box')]
class Box
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['box:list', 'box:detail'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['box:list', 'box:detail'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $zipCode = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $country = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['box:detail'])]
    private ?float $lat = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['box:detail'])]
    private ?float $lng = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $website = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $facebook = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['box:detail'])]
    private ?string $instagram = null;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;
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

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;
        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;
        return $this;
    }
}
