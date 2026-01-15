<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Entity\WodVariant;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

class WodScoreDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public int $id;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public UserDTO $user;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public WodDTO $wod;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public WodVariantDTO $variant;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public ?int $time;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public ?int $repetitions;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public ?int $weight;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public DateTimeImmutable $performedAt;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
    ])]
    public ?string $notes;

    #[Groups([
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
    ])]
    public bool $private;

    public function __construct(
        int               $id,
        UserDTO           $user,
        WodDTO            $wod,
        WodVariantDTO     $variant,
        ?int              $time,
        ?int              $repetitions,
        ?int              $weight,
        DateTimeImmutable $performedAt,
        ?string           $notes,
        bool              $private,
    ) {
        parent::__construct($id);

        $this->user        = $user;
        $this->variant     = $variant;
        $this->wod         = $wod;
        $this->time        = $time;
        $this->repetitions = $repetitions;
        $this->weight      = $weight;
        $this->performedAt = $performedAt;
        $this->notes       = $notes;
        $this->private     = $private;
    }
}
