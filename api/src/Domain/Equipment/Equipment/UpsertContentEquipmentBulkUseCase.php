<?php

namespace App\Domain\Equipment\Equipment;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Ports\EquipmentDALInterface;
use App\UI\Adapters\Http\Equipment\Equipment\UpsertContentEquipmentBulkHttp;
use App\UI\Adapters\Http\Equipment\Equipment\UpsertContentEquipmentHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentEquipmentBulkUseCase
{
    public function __construct(
        private DatabaseInterface             $database,
        private EquipmentDALInterface         $equipmentDAL,
        private UpsertContentEquipmentUseCase $upsertContentEquipmentUseCase,
    ) {}

    public function execute(UpsertContentEquipmentBulkHttp $dto): void
    {
        $equipment = $this->equipmentDAL->getById($dto->getId());
        if (!$equipment instanceof Equipment) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentEquipmentUseCase->execute(
                    new UpsertContentEquipmentHttp(
                        id     : $dto->getId(),
                        locale : $locale,
                        payload: $contentData
                    )
                );
            }

            $this->database->commit();
        } catch (\Throwable $e) {
            $this->database->rollback();
            throw $e;
        }
    }

    private function validatePayload(array $contents): void
    {
        if (empty($contents)) {
            throw new InvalidPayloadException();
        }

        foreach ($contents as $locale => $content) {
            if (!preg_match('/^[a-z]{2}$/', $locale)) {
                throw new InvalidPayloadException();
            }

            if (!is_array($content)) {
                throw new InvalidPayloadException();
            }
        }
    }
}
