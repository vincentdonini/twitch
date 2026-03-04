<?php

namespace App\Domain\Organization\Company;

use Symfony\Component\Uid\Uuid;

interface GetCompanyByIdDTOInterface
{
    public function getId(): Uuid;
}
