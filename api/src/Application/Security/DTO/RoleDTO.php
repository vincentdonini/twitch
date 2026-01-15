<?php

namespace App\Application\Security\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class RoleDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::ROLE_LIST, FrontGroupsEnum::ROLE_DETAIL,
    ])]
    public string $code;

    #[Groups([
        FrontGroupsEnum::ROLE_LIST, FrontGroupsEnum::ROLE_DETAIL,
    ])]
    public string $label;

    public function __construct(
        int    $id,
        string $code,
        string $label,
    ) {
        parent::__construct($id);

        $this->code  = $code;
        $this->label = $label;
    }
}

