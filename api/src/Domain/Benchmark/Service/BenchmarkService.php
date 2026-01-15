<?php

namespace App\Domain\Benchmark\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Benchmark\DTO\BenchmarkDTO;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Exercise\Service\ExerciseService;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class BenchmarkService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack    $requestStack,
        private ExerciseService $exerciseService,
    ) {
    }

    public function transformToDTO(Benchmark $benchmark, FilterCollection $filters = null): ?BenchmarkDTO
    {
        $content = $benchmark->getContentByLocale($this->getLocale());

        return new BenchmarkDTO(
            id      : $benchmark->getId(),
            name    : $benchmark->getName(),
            type    : $benchmark->getType(),
            title   : $content ? $content->getTitle() : '',
            summary : $content ? $content->getSummary() : '',
            details : $content ? $content->getDetails() : '',
            rules   : $content ? $content->getRules() : '',
            tips    : $content ? $content->getTips() : '',
            exercise: $this->exerciseService->transformToDTO($benchmark->getExercise(), $filters),
        );
    }

    public function transformCollectionToDTO(array $benchmarks, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $benchmarks,
            fn(Benchmark $benchmark) => $this->transformToDTO($benchmark, $filters)
        );
    }
}