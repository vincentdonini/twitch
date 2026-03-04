<?php

namespace App\Application\Exercise\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class ExerciseCategoryDTO
{
    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public string $details;

    public function __construct(
        Uuid   $id,
        string $slug,
        string $title,
        string $summary,
        string $details
    ) {
        $this->id      = $id;
        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
    }
}
