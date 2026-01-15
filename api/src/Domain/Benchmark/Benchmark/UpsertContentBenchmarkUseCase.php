<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Content\Entity\ContentBenchmark;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpsertContentBenchmarkHttp;
use Doctrine\ORM\EntityNotFoundException;

readonly class UpsertContentBenchmarkUseCase
{
    public function __construct(
        private DatabaseInterface     $database,
        private BenchmarkDALInterface $benchmarkDAL,
    ) {
    }

    public function execute(
        UpsertContentBenchmarkHttp $dto
    ): ContentBenchmark {
        $benchmark = $this->benchmarkDAL->getById($dto->getId());
        if (!$benchmark instanceof Benchmark) {
            throw new EntityNotFoundException();
        }

        if (!$this->validatePayload($dto)) {
            throw new InvalidPayloadException();
        }

        $contentBenchmark = $benchmark->getContentByLocale($dto->getLocale());

        if (!$contentBenchmark) {
            $contentBenchmark = new ContentBenchmark(
                benchmark: $benchmark,
                locale   : $dto->getLocale(),
                title    : $dto->getTitle(),
                summary  : $dto->getSummary(),
                details  : $dto->getDetails()
            );

            $benchmark->addContent($contentBenchmark);
        } else {
            $contentBenchmark
                ->setTitle($dto->getTitle())
                ->setSummary($dto->getSummary());

            if ($dto->getDetails() !== null) {
                $contentBenchmark->setDetails($dto->getDetails());
            }
        }

        $this->database->preSave($contentBenchmark);
        $this->database->save();

        return $contentBenchmark;
    }

    private function validatePayload(UpsertContentBenchmarkHttp $dto): bool
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

