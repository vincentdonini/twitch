<?php

namespace App\Domain\Organization\Company;

use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListCompanyUseCase
{
    public function __construct(
        private CompanyDALInterface $companyDAL,
    ) {
    }

    public function execute(ListCompanyDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->companyDAL->listCompanies(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

