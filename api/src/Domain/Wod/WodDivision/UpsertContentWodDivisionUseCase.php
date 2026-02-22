<?php

namespace App\Domain\Wod\WodDivision;

use App\Domain\Content\Entity\ContentWodDivision;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\UI\Adapters\Http\Wod\WodDivision\UpsertContentWodDivisionHttp;
use Doctrine\ORM\EntityNotFoundException;

final readonly class UpsertContentWodDivisionUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodDivisionDALInterface $wodDivisionDAL,
    ) {
    }

    public function execute(
        UpsertContentWodDivisionHttp $dto
    ): ContentWodDivision {
        $wodDivision = $this->wodDivisionDAL->getById($dto->getId());
        if (!$wodDivision instanceof WodDivision) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentWodDivision = $wodDivision->getContentByLocale($dto->getLocale());

        if (!$contentWodDivision) {
            $contentWodDivision = new ContentWodDivision(
                wodDivision: $wodDivision,
                locale     : $dto->getLocale(),
                title      : $dto->getTitle(),
                summary    : $dto->getSummary(),
                details    : $dto->getDetails()
            );

            $wodDivision->addContent($contentWodDivision);
        } else {
            $contentWodDivision
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentWodDivision->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentWodDivision);
        $this->database->save();

        return $contentWodDivision;
    }

    private function validatePayload(UpsertContentWodDivisionHttp $dto): bool
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

