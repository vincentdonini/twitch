<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class CompareUserAchievementsResponseDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public UserComparisonDTO $user;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public UserComparisonDTO $other;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public SummaryComparisonDTO $summary;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public array $categories;

    public function __construct(
        UserComparisonDTO    $user,
        UserComparisonDTO    $other,
        SummaryComparisonDTO $summary,
        array                $categories,
    ) {
        $this->user       = $user;
        $this->other      = $other;
        $this->summary    = $summary;
        $this->categories = $categories;
    }
}
