<?php

namespace App\Application\Wod\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public string $name;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $rules;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $tips;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public WodTypeDTO $type;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public WodCategoryDTO $category;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public array $variants;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public ?int $teamSize;

    public function __construct(
        Uuid           $id,
        string         $name,
        string         $title,
        string         $summary,
        string         $details,
        string         $rules,
        string         $tips,
        WodTypeDTO     $type,
        WodCategoryDTO $category,
        array          $variants,
        ?int           $teamSize,
    ) {
        $this->id       = $id;
        $this->name     = $name;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->rules    = $rules;
        $this->tips     = $tips;
        $this->type     = $type;
        $this->category = $category;
        $this->variants = $variants;
        $this->teamSize = $teamSize;
    }
}
