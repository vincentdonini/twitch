<?php

namespace App\Domain\Wod\WodType;

use App\Domain\Content\Entity\ContentWodType;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Ports\WodTypeDALInterface;
use App\UI\Adapters\Http\Wod\WodType\UpsertContentWodTypeHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentWodTypeUseCase
{
    public function __construct(
        private DatabaseInterface   $database,
        private WodTypeDALInterface $wodTypeDAL,
    ) {
    }

    public function execute(
        UpsertContentWodTypeHttp $dto
    ): ContentWodType {
        $wodType = $this->wodTypeDAL->getById($dto->getId());
        if (!$wodType instanceof WodType) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentWodType = $wodType->getContentByLocale($dto->getLocale());

        if (!$contentWodType) {
            $contentWodType = new ContentWodType(
                wodType: $wodType,
                locale : $dto->getLocale(),
                title  : $dto->getTitle(),
                summary: $dto->getSummary(),
                details: $dto->getDetails()
            );

            $wodType->addContent($contentWodType);
        } else {
            $contentWodType
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentWodType->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentWodType);
        $this->database->save();

        return $contentWodType;
    }

    private function validatePayload(UpsertContentWodTypeHttp $dto): bool
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

