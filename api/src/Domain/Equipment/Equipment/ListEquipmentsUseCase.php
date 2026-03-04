<?php

namespace App\Domain\Equipment\Equipment;

use App\Domain\Equipment\Ports\EquipmentDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListEquipmentsUseCase
{
    public function __construct(
        private EquipmentDALInterface $equipmentDAL,
    ) {
    }

    public function execute(ListEquipmentsDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->equipmentDAL->listEquipments(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
