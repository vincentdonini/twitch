<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\User\Ports\UserDALInterface;

final readonly class CreateBenchmarkScoreUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private UserDALInterface      $userDAL,
        private BenchmarkDALInterface $benchmarkDAL,
    ) {
    }

    public function execute(CreateBenchmarkScoreDTOInterface $dto): BenchmarkScore
    {
        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $user      = $this->userDAL->getById($dto->getUserId());
        $benchmark = $this->benchmarkDAL->getById($dto->getBenchmarkId());

        if (!$benchmark || !$user) {
            throw new InvalidPayloadException();
        }

        $benchmarkScore = new BenchmarkScore(
            user       : $user,
            benchmark  : $benchmark,
            performedAt: new \DateTimeImmutable($dto->getPerformedAt())
        );

        $benchmarkScore->setTime($dto->getTime() ?? null);
        $benchmarkScore->setRepetitions($dto->getRepetitions() ?? null);
        $benchmarkScore->setWeight($dto->getWeight() ?? null);

        $benchmarkScore->setIsPrivate($dto->isPrivate() ?? false);

        $benchmarkScore->assertScoreIsValid();

        $this->database->preSave($benchmarkScore);
        $this->database->save();

        return $benchmarkScore;
    }

    private function validatePayload(CreateBenchmarkScoreDTOInterface $dto): bool
    {
        if (
            !$dto->getUserId() ||
            !$dto->getBenchmarkId() ||
            !$dto->getPerformedAt()
        ) {
            return false;
        }
        return true;
    }
}
