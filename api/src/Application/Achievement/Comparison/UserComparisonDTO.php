<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

class UserComparisonDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public string $firstName;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public string $lastName;

    public function __construct(
        Uuid   $id,
        string $firstName,
        string $lastName,
    ) {
        $this->id        = $id;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }
}
