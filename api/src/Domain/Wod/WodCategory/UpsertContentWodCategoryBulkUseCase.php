<?php

namespace App\Domain\Wod\WodCategory;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\UI\Adapters\Http\Wod\WodCategory\UpsertContentWodCategoryBulkHttp;
use App\UI\Adapters\Http\Wod\WodCategory\UpsertContentWodCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentWodCategoryBulkUseCase
{
    public function __construct(
        private DatabaseInterface               $database,
        private WodCategoryDALInterface         $wodCategoryDAL,
        private UpsertContentWodCategoryUseCase $upsertContentWodCategoryUseCase,
    ) {
    }

    public function execute(UpsertContentWodCategoryBulkHttp $dto): void
    {
        $wodCategory = $this->wodCategoryDAL->getById($dto->getId());
        if (!$wodCategory instanceof WodCategory) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentWodCategoryUseCase->execute(
                    new UpsertContentWodCategoryHttp(
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
