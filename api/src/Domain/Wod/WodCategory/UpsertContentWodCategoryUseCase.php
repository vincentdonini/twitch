<?php

namespace App\Domain\Wod\WodCategory;

use App\Domain\Content\Entity\ContentWodCategory;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\UI\Adapters\Http\Wod\WodCategory\UpsertContentWodCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentWodCategoryUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodCategoryDALInterface $wodCategoryDAL,
    ) {
    }

    public function execute(
        UpsertContentWodCategoryHttp $dto
    ): ContentWodCategory {
        $wodCategory = $this->wodCategoryDAL->getById($dto->getId());
        if (!$wodCategory instanceof WodCategory) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentWodCategory = $wodCategory->getContentByLocale($dto->getLocale());

        if (!$contentWodCategory) {
            $contentWodCategory = new ContentWodCategory(
                wodCategory: $wodCategory,
                locale     : $dto->getLocale(),
                title      : $dto->getTitle(),
                summary    : $dto->getSummary(),
                details    : $dto->getDetails()
            );

            $wodCategory->addContent($contentWodCategory);
        } else {
            $contentWodCategory
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentWodCategory->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentWodCategory);
        $this->database->save();

        return $contentWodCategory;
    }

    private function validatePayload(UpsertContentWodCategoryHttp $dto): bool
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

