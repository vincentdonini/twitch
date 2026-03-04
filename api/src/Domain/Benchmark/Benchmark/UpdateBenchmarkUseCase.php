<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Enum\TypeEnum;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateBenchmarkUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private BenchmarkDALInterface $benchmarkDAL,
        private ExerciseDALInterface  $exerciseDAL,
    ) {
    }

    public function execute(
        UpdateBenchmarkDTOInterface $dto
    ): Benchmark {
        $benchmark = $this->benchmarkDAL->getById(Uuid::fromString($dto->getId()));
        if (!$benchmark instanceof Benchmark) {
            throw new EntityNotFoundException();
        }

        if (!empty($dto->getExerciseId())) {
            $exercise = $this->exerciseDAL->getById($dto->getExerciseId());
            $benchmark->setExercise($exercise);
        }

        if (!empty($dto->getName())) {
            if (!$this->validateDuplicateField($dto->getName(), $benchmark->getId())) {
                throw new AlreadyExistException();
            }
            $benchmark->setName($dto->getName());
        }

        if (!empty($dto->getType())) {
            $typeEnum = TypeEnum::tryFrom($dto->getType());
            if (!$typeEnum) {
                throw new InvalidPayloadException();
            }
            $benchmark->setType($typeEnum);
        }

        if (!empty($dto->getValue())) {
            $benchmark->setValue($dto->getValue());
        }

        $this->database->preSave($benchmark);
        $this->database->save();

        return $benchmark;
    }

    private function validateDuplicateField(string $value, Uuid $currentBenchmarkId): bool
    {
        $existingBenchmark = $this->benchmarkDAL->findOneBy(['name' => $value]);

        if (!$existingBenchmark) {
            return true;
        }

        return $existingBenchmark->getId() === $currentBenchmarkId;
    }
}
