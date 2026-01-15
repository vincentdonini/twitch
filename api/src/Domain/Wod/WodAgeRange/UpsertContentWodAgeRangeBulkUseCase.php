<?php

namespace App\Domain\Wod\WodAgeRange;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\UI\Adapters\Http\Wod\WodAgeRange\UpsertContentWodAgeRangeBulkHttp;
use App\UI\Adapters\Http\Wod\WodAgeRange\UpsertContentWodAgeRangeHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentWodAgeRangeBulkUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private WodAgeRangeDALInterface         $wodAgeRangeDAL,
        private UpsertContentWodAgeRangeUseCase $upsertContentWodAgeRangeUseCase,
    ) {
    }

    public function execute(UpsertContentWodAgeRangeBulkHttp $dto): void
    {
        $wodType = $this->wodAgeRangeDAL->getById($dto->getId());
        if (!$wodType instanceof WodAgeRange) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentWodAgeRangeUseCase->execute(
                    new UpsertContentWodAgeRangeHttp(
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
