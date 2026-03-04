<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\Shared\Utils\StringHelper;

final readonly class CreateBenchmarkUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private BenchmarkDALInterface $benchmarkDAL,
        private ExerciseDALInterface  $exerciseDAL,
    ) {
    }

    public function execute(CreateBenchmarkDTOInterface $dto): Benchmark
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        if (!$this->validateDuplicate($dto->getName())) {
            throw new AlreadyExistException();
        }

        $exercise = $this->exerciseDAL->getById($dto->getExerciseId());

        $benchmark = new Benchmark(
            exercise: $exercise,
            slug    : StringHelper::slugify($dto->getName()),
            name    : $dto->getName(),
            type    : $dto->getType(),
        );

        $this->database->preSave($benchmark);
        $this->database->save();

        return $benchmark;
    }

    private function validatePayload(CreateBenchmarkDTOInterface $dto): bool
    {
        if (
            !$dto->getExerciseId() ||
            !$dto->getName() ||
            !$dto->getType()
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $name): bool
    {
        return !$this->benchmarkDAL->findOneBy(['name' => $name]);
    }
}
