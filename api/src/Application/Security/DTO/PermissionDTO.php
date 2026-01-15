<?php

namespace App\Application\Security\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class PermissionDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::PERMISSION_LIST, FrontGroupsEnum::PERMISSION_DETAIL,
        FrontGroupsEnum::ROLE_PERMISSION_LIST, FrontGroupsEnum::ROLE_PERMISSION_DETAIL,
    ])]
    public string $code;

    #[Groups([
        FrontGroupsEnum::PERMISSION_LIST, FrontGroupsEnum::PERMISSION_DETAIL,
        FrontGroupsEnum::ROLE_PERMISSION_LIST, FrontGroupsEnum::ROLE_PERMISSION_DETAIL,
    ])]
    public string $label;

    #[Groups([
        FrontGroupsEnum::PERMISSION_LIST, FrontGroupsEnum::PERMISSION_DETAIL,
        FrontGroupsEnum::ROLE_PERMISSION_LIST, FrontGroupsEnum::ROLE_PERMISSION_DETAIL,
    ])]
    public ?string $resource;

    public function __construct(
        int    $id,
        string $code,
        string $label,
        string $resource,
    ) {
        parent::__construct($id);

        $this->code     = $code;
        $this->label    = $label;
        $this->resource = $resource;
    }
}

