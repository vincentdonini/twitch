<?php

namespace App\Domain\Equipment\Equipment;

use App\Domain\Content\Entity\ContentEquipment;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Ports\EquipmentDALInterface;

final readonly class UpsertContentEquipmentBulkUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private EquipmentDALInterface $equipmentDAL,
    ) {}

    public function execute(UpsertContentEquipmentBulkDTOInterface $dto): void
    {
        $equipment = $this->equipmentDAL->getById($dto->getId());
        if (!$equipment instanceof Equipment) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $contentEquipment = $equipment->getContentByLocale($locale);

                if (!$contentEquipment) {
                    $contentEquipment = new ContentEquipment(
                        equipment: $equipment,
                        locale   : $locale,
                        title    : $contentData['title'] ?? '',
                        summary  : $contentData['summary'] ?? '',
                        details  : $contentData['details'] ?? null,
                    );
                    $equipment->addContent($contentEquipment);
                } else {
                    $contentEquipment->setTitle($contentData['title'] ?? '');
                    $contentEquipment->setSummary($contentData['summary'] ?? '');
                    if (array_key_exists('details', $contentData)) {
                        $contentEquipment->setDetails($contentData['details']);
                    }
                }

                $this->database->preSave($contentEquipment);
            }

            $this->database->save();
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
