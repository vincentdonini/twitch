<?php

namespace App\Application\Exercise\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class ExerciseCategoryDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::EXERCISE_CATEGORY_LIST, FrontGroupsEnum::EXERCISE_CATEGORY_DETAIL,
    ])]
    public int $id;

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
        int    $id,
        string $slug,
        string $title,
        string $summary,
        string $details
    ) {
        parent::__construct($id);

        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
    }
}
