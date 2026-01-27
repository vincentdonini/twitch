<?php

namespace App\Domain\Organization\Company;

use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Ports\CompanyDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetCompanyByIdUseCase
{
    public function __construct(
        private readonly CompanyDALInterface $companyDAL,
    ) {

    }

    public function execute(GetCompanyByIdDTOInterface $dto): Company
    {
        $company = $this->companyDAL->getById($dto->getId());
        if (!$company instanceof Company) {
            throw new EntityNotFoundException();
        }

        return $company;
    }
}

