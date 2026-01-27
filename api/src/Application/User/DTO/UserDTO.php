<?php

namespace App\Application\User\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class UserDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::USER_LIST, FrontGroupsEnum::USER_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
        FrontGroupsEnum::ATHLETE_LIST,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::USER_LIST, FrontGroupsEnum::USER_DETAIL,
    ])]
    public string $email;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::USER_LIST, FrontGroupsEnum::USER_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
        FrontGroupsEnum::ATHLETE_LIST,
    ])]
    public string $firstName;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::USER_LIST, FrontGroupsEnum::USER_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
        FrontGroupsEnum::ATHLETE_LIST,
    ])]
    public string $lastName;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ATHLETE_LIST,
    ])]
    public array $roles;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public array $permissions;

    public function __construct(
        Uuid   $id,
        string $email,
        string $firstName,
        string $lastName,
        array  $roles = [],
        array  $permissions = [],
    ) {
        $this->id          = $id;
        $this->email       = $email;
        $this->firstName   = $firstName;
        $this->lastName    = $lastName;
        $this->roles       = $roles;
        $this->permissions = $permissions;
    }
}
