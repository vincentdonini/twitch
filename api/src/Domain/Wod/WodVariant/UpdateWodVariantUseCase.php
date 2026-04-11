<?php

namespace App\Domain\Wod\WodVariant;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Entity\WodVariantExercise;
use App\Domain\Wod\Entity\WodVariantExerciseMetric;
use App\Domain\Wod\Enum\GenderEnum;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Domain\Wod\Ports\WodVariantDALInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateWodVariantUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodVariantDALInterface  $wodVariantDAL,
        private WodDivisionDALInterface $wodDivisionDAL,
        private WodAgeRangeDALInterface $wodAgeRangeDAL,
        private ExerciseDALInterface    $exerciseDAL,
    ) {
    }

    public function execute(UpdateWodVariantDTOInterface $dto): void
    {
        $variant = $this->wodVariantDAL->getById($dto->getVariantId());
        if (!$variant instanceof WodVariant) {
            throw new EntityNotFoundException();
        }

        if ($dto->getDivisionId()) {
            $division = $this->wodDivisionDAL->getById($dto->getDivisionId());
            if ($division) {
                $variant->setWodDivision($division);
            }
        }

        if ($dto->getGender() !== null) {
            $variant->setGender(GenderEnum::from($dto->getGender()));
        }

        if ($dto->getAgeRangeId() !== null) {
            $ageRange = $this->wodAgeRangeDAL->getById($dto->getAgeRangeId());
            $variant->setWodAgeRange($ageRange);
        }

        if ($dto->getRounds() !== null) {
            $variant->setRounds($dto->getRounds());
        }

        if ($dto->getTimeCap() !== null) {
            $variant->setTimeCap($dto->getTimeCap());
        }

        if ($dto->getExercises() !== null) {
            $variant->getWodVariantExercises()->clear();

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
        }

        $this->database->preSave($variant);
        $this->database->save();
    }
}
