<?php

namespace App\Application\User\DTO;

use App\Domain\Organization\Entity\Place;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class UserMePlaceDTO
{
    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $id;

    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $name;

    public static function fromPlace(Place $place): self
    {
        $dto       = new self();
        $dto->id   = (string) $place->getId();
        $dto->name = $place->getName();

        return $dto;
    }
}
