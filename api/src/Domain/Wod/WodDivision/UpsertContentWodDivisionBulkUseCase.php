<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\UI\Adapters\Http\Wod\WodDivision\UpsertContentWodDivisionBulkHttp;
use App\UI\Adapters\Http\Wod\WodDivision\UpsertContentWodDivisionHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentWodDivisionBulkUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private WodDivisionDALInterface         $wodDivisionDAL,
        private UpsertContentWodDivisionUseCase $upsertContentWodDivisionUseCase,
    ) {
    }

    public function execute(UpsertContentWodDivisionBulkHttp $dto): void
    {
        $wodDivision = $this->wodDivisionDAL->getById($dto->getId());
        if (!$wodDivision instanceof WodDivision) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentWodDivisionUseCase->execute(
                    new UpsertContentWodDivisionHttp(
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
