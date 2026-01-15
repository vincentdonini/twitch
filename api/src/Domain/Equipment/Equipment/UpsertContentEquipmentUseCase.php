<?php

namespace App\Domain\Equipment\Equipment;

use App\Domain\Content\Entity\ContentEquipment;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Ports\EquipmentDALInterface;
use App\UI\Adapters\Http\Equipment\Equipment\UpsertContentEquipmentHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentEquipmentUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private EquipmentDALInterface $equipmentDAL,
    ) {
    }

    public function execute(
        UpsertContentEquipmentHttp $dto
    ): ContentEquipment {
        $equipment = $this->equipmentDAL->getById($dto->getId());
        if (!$equipment instanceof Equipment) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentEquipment = $equipment->getContentByLocale($dto->getLocale());

        if (!$contentEquipment) {
            $contentEquipment = new ContentEquipment(
                equipment: $equipment,
                locale   : $dto->getLocale(),
                title    : $dto->getTitle(),
                summary  : $dto->getSummary(),
                details  : $dto->getDetails()
            );

            $equipment->addContent($contentEquipment);
        } else {
            $contentEquipment
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentEquipment->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentEquipment);
        $this->database->save();

        return $contentEquipment;
    }

    private function validatePayload(UpsertContentEquipmentHttp $dto): bool
    {
        if (
            !$dto->getLocale() ||
            !$dto->getTitle() ||
            !$dto->getSummary()
        ) {
            return false;
        }
        return true;
    }
}

