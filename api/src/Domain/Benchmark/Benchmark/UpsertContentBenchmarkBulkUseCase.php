<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpsertContentBenchmarkBulkHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpsertContentBenchmarkHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentBenchmarkBulkUseCase
{
    public function __construct(
        private DatabaseInterface             $database,
        private BenchmarkDALInterface         $benchmarkDAL,
        private UpsertContentBenchmarkUseCase $upsertContentBenchmarkUseCase,
    ) {
    }

    public function execute(
        UpsertContentBenchmarkBulkHttp $dto
    ): void {
        $benchmark = $this->benchmarkDAL->getById($dto->getId());
        if (!$benchmark instanceof Benchmark) {
            throw new EntityNotFoundException();
        }

        $this->validatePayload($dto->getContents());

        $this->database->beginTransaction();

        try {
            foreach ($dto->getContents() as $locale => $contentData) {
                $this->upsertContentBenchmarkUseCase->execute(
                    new UpsertContentBenchmarkHttp(
                        id     : $dto->getId(),
                        locale : $locale,
                        payload: $contentData
                    )
                );
            }

            $this->database->commit();
        } catch (\Throwable $e) {
            $this->database->rollback();
            throw $e;
        }
    }

    private function validatePayload(array $contents): void
    {
        if (empty($contents)) {
            throw new InvalidPayloadException();
        }

        foreach ($contents as $locale => $content) {
            if (!preg_match('/^[a-z]{2}$/', $locale)) {
                throw new InvalidPayloadException();
            }

            if (!is_array($content)) {
                throw new InvalidPayloadException();
            }
        }
    }
}
