<?php

namespace App\Application\User\DTO;

use App\Domain\Organization\Entity\Company;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class UserMeCompanyDTO
{
    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $id;

    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $name;

    /** @var UserMePlaceDTO[] */
    #[Groups([FrontGroupsEnum::USER_ME])]
    public array $places = [];

    public static function fromCompany(Company $company): self
    {
        $dto         = new self();
        $dto->id     = (string) $company->getId();
        $dto->name   = $company->getName();
        $dto->places = $company->getPlaces()
            ->map(fn($place) => UserMePlaceDTO::fromPlace($place))
            ->toArray();

        return $dto;
    }
}
