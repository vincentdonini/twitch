<?php

namespace App\UI\Adapters\Http\Organization\Company;

use App\Domain\Organization\Company\GetCompanyByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetCompanyByIdHttp implements GetCompanyByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
