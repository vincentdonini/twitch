<?php

namespace App\Domain\Wod\WodType;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Ports\WodTypeDALInterface;
use App\UI\Adapters\Http\Wod\WodType\UpsertContentWodTypeBulkHttp;
use App\UI\Adapters\Http\Wod\WodType\UpsertContentWodTypeHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentWodTypeBulkUseCase
{
    public function __construct(
        private DatabaseInterface           $database,
        private WodTypeDALInterface         $wodTypeDAL,
        private UpsertContentWodTypeUseCase $upsertContentWodTypeUseCase,
    ) {
    }

    public function execute(UpsertContentWodTypeBulkHttp $dto): void
    {
        $wodType = $this->wodTypeDAL->getById($dto->getId());
        if (!$wodType instanceof WodType) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentWodTypeUseCase->execute(
                    new UpsertContentWodTypeHttp(
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
