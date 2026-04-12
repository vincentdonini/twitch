<?php

namespace App\Domain\Wod\WodVariant;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Entity\WodVariantExercise;
use App\Domain\Wod\Entity\WodVariantExerciseMetric;
use App\Domain\Wod\Enum\GenderEnum;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Domain\Wod\Ports\WodVariantDALInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CreateWodVariantUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodDALInterface         $wodDAL,
        private WodDivisionDALInterface $wodDivisionDAL,
        private WodVariantDALInterface  $wodVariantDAL,
        private WodAgeRangeDALInterface $wodAgeRangeDAL,
        private ExerciseDALInterface    $exerciseDAL,
    ) {
    }

    public function execute(CreateWodVariantDTOInterface $dto): WodVariant
    {
        if (!$dto->getWodId() || !$dto->getDivisionId()) {
            throw new InvalidPayloadException();
        }

        $wod = $this->wodDAL->getById($dto->getWodId());
        if (!$wod) {
            throw new EntityNotFoundException('WOD not found with id ' . $dto->getWodId());
        }

        $division = $this->wodDivisionDAL->getById($dto->getDivisionId());
        if (!$division) {
            throw new EntityNotFoundException('Division not found with id ' . $dto->getDivisionId());
        }

        $ageRange = null;
        if ($dto->getAgeRangeId()) {
            $ageRange = $this->wodAgeRangeDAL->getById($dto->getAgeRangeId());
        }

        $gender = $dto->getGender() ? GenderEnum::from($dto->getGender()) : null;

        $existing = $this->wodVariantDAL->getByCriteria($wod, $division, $ageRange, $gender);
        if ($existing) {
            throw new AlreadyExistException('WOD already exists with id ' . $dto->getWodId());
        }

        $variant = new WodVariant($wod, $division);
        $variant->setGender($gender);
        $variant->setWodAgeRange($ageRange);
        $variant->setRounds($dto->getRounds() ?? null);
        $variant->setTimeCap($dto->getTimeCap() ?? null);

        foreach ($dto->getExercises() as $i => $data) {
            $exercise = $this->exerciseDAL->getById(Uuid::fromString($data['exerciseId']));
            if (!$exercise) {
                continue;
            }

            $variantExercise = new WodVariantExercise($i + 1, $variant, $exercise);

            foreach ($data['metrics'] ?? [] as $m) {
                $variantExercise->addMetric(
                    new WodVariantExerciseMetric($variantExercise, $m['type'], (float) $m['value'])
                );
            }

            $variant->addWodVariantExercise($variantExercise);
        }

        $this->database->preSave($variant);
        $this->database->save();

        return $variant;
    }
}
