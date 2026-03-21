<?php

namespace App\Domain\Wod\WodScore;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\User\Ports\UserDALInterface;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Ports\WodVariantDALInterface;

final readonly class CreateWodScoreUseCase
{
    public function __construct(
        private DatabaseInterface      $database,
        private UserDALInterface       $userDAL,
        private WodVariantDALInterface $wodVariantDAL,
    ) {
    }

    public function execute(CreateWodScoreDTOInterface $dto): WodScore
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $user       = $this->userDAL->getById($dto->getUserId());
        $wodVariant = $this->wodVariantDAL->getById($dto->getWodVersionId());

        if (
            !$user ||
            !$wodVariant ||
            $wodVariant->getWod()->getId()->toRfc4122() !== $dto->getWodId()->toRfc4122()
        ) {
            throw new InvalidPayloadException();
        }

        $wodScore = new WodScore(
            user       : $user,
            wod        : $wodVariant->getWod(),
            wodVariant : $wodVariant,
            performedAt: new \DateTimeImmutable($dto->getPerformedAt())
        );

        $wodScore->setTime($dto->getTime() ?? null);
        $wodScore->setRepetitions($dto->getRepetitions() ?? null);
        $wodScore->setWeight($dto->getWeight() ?? null);

        $wodScore->setIsPrivate($dto->isPrivate() ?? false);

        $wodScore->assertScoreIsValid();

        $this->database->preSave($wodScore);
        $this->database->save();

        return $wodScore;
    }

    private function validatePayload(CreateWodScoreDTOInterface $dto): bool
    {
        if (
            !$dto->getUserId() ||
            !$dto->getWodId() ||
            !$dto->getWodVersionId() ||
            !$dto->getPerformedAt()
        ) {
            return false;
        }
        return true;
    }
}
