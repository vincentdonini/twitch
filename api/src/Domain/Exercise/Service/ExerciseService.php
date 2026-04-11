<?php

namespace App\Domain\Exercise\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Exercise\DTO\ExerciseDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Equipment\Service\EquipmentService;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Muscle\Service\MuscleService;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final class ExerciseService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack            $requestStack,
        private readonly EntityFieldFilter       $EntityFieldFilter,
        private readonly EquipmentService        $equipmentService,
        private readonly ExerciseCategoryService $exerciseCategoryService,
        private readonly MuscleService           $muscleService,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(Exercise $exercise) => $exercise->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(Exercise $exercise) => $exercise->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(Exercise $exercise) => $exercise->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(Exercise $exercise, FilterCollection $filters = null, int $wodCount = 0): ?ExerciseDTO
    {
        $content = $exercise->getContentByLocale($this->getLocale());
        if (!$this->EntityFieldFilter->match($exercise, $filters, $this->filterMapping)) {
            return null;
        }

        $equipment = $exercise->getEquipment()
            ? $this->equipmentService->transformToDTO($exercise->getEquipment())
            : null;

        $exerciseCategory = $this->exerciseCategoryService->transformToDTO($exercise->getExerciseCategory());

        $muscles = $this->muscleService->transformCollectionToDTO($exercise->getMuscles()->toArray());

        return new ExerciseDTO(
            id              : $exercise->getId(),
            slug            : $exercise->getSlug(),
            title           : $content ? $content->getTitle() : '',
            summary         : $content ? $content->getSummary() : '',
            details         : $content ? $content->getDetails() : '',
            wodCount        : $wodCount,
            equipment       : $equipment,
            exerciseCategory: $exerciseCategory,
            muscles         : $muscles,
            titlePlural     : $content ? $content->getTitlePlural() : null,
        );
    }

    public function transformCollectionToDTO(array $exercises, FilterCollection $filters = null, array $wodCounts = []): array
    {
        return CollectionMapper::mapAndFilter(
            $exercises,
            fn(Exercise $exercise) => $this->transformToDTO($exercise, $filters, $wodCounts[(string)$exercise->getId()] ?? 0)
        );
    }
}
