<?php

namespace App\Domain\Wod\WodAgeRange;

use App\Domain\Content\Entity\ContentWodAgeRange;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\UI\Adapters\Http\Wod\WodAgeRange\UpsertContentWodAgeRangeHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentWodAgeRangeUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodAgeRangeDALInterface $wodAgeRangeDAL,
    ) {
    }

    public function execute(
        UpsertContentWodAgeRangeHttp $dto
    ): ContentWodAgeRange {
        $wodAgeRange = $this->wodAgeRangeDAL->getById($dto->getId());
        if (!$wodAgeRange instanceof WodAgeRange) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentWodAgeRange = $wodAgeRange->getContentByLocale($dto->getLocale());

        if (!$contentWodAgeRange) {
            $contentWodAgeRange = new ContentWodAgeRange(
                wodAgeRange: $wodAgeRange,
                locale     : $dto->getLocale(),
                title      : $dto->getTitle(),
                summary    : $dto->getSummary(),
                details    : $dto->getDetails()
            );

            $wodAgeRange->addContent($contentWodAgeRange);
        } else {
            $contentWodAgeRange
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentWodAgeRange->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentWodAgeRange);
        $this->database->save();

        return $contentWodAgeRange;
    }

    private function validatePayload(UpsertContentWodAgeRangeHttp $dto): bool
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

