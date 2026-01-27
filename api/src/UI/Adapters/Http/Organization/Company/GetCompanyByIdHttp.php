<?php

namespace App\UI\Adapters\Http\Organization\Company;

use App\Domain\Organization\Company\GetCompanyByIdDTOInterface;

final readonly class GetCompanyByIdHttp implements GetCompanyByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
