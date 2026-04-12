<?php

namespace App\Domain\Wod\WodVariant;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Ports\WodVariantDALInterface;

final readonly class DeleteWodVariantUseCase
{
    public function __construct(
        private DatabaseInterface      $database,
        private WodVariantDALInterface $wodVariantDAL,
    ) {
    }

    public function execute(DeleteWodVariantDTOInterface $dto): void
    {
        $variant = $this->wodVariantDAL->getById($dto->getVariantId());
        if (!$variant instanceof WodVariant) {
            throw new EntityNotFoundException();
        }

        $this->database->remove($variant);
        $this->database->save();
    }
}
