<?php

namespace App\Domain\Wod\WodCategory;

use Symfony\Component\Uid\Uuid;

interface GetWodCategoryByIdDTOInterface
{
    public function getId(): Uuid;
}
