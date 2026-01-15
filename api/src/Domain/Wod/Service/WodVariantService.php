<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodVariantDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Wod\Entity\WodVariant;
use App\Infrastructure\Filters\FilterCollection;

final readonly class WodVariantService
{
    private array $filterMapping;

    public function __construct(
        private WodAgeRangeService        $wodAgeRangeService,
        private WodDivisionService        $wodDivisionService,
        private WodVariantExerciseService $wodVariantExerciseService,
        private EntityFieldFilter         $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'wodVariants.wodDivision.id'   => fn(WodVariant $wodVariant) => $wodVariant->getWodDivision()->getId(),
            'wodVariants.wodDivision.slug' => fn(WodVariant $wodVariant) => $wodVariant->getWodDivision()->getSlug(),
            'wodVariants.gender'           => fn(WodVariant $wodVariant) => $wodVariant->getGender(),
        ];
    }

    public function transformToDTO(WodVariant $wodVariant, FilterCollection $filters = null): ?WodVariantDTO
    {
        if (!$this->EntityFieldFilter->match($wodVariant, $filters, $this->filterMapping)) {
            return null;
        }

        $wodVariantExerciseDTOs = $this->wodVariantExerciseService->transformCollectionToDTO(
            $wodVariant->getWodVariantExercises()->toArray(),
            $filters
        );

        return new WodVariantDTO(
            id       : $wodVariant->getId(),
            division : $this->wodDivisionService->transformToDTO($wodVariant->getWodDivision(), $filters),
            gender   : $wodVariant->getGender(),
            ageRange : $wodVariant->getWodAgeRange() ? $this->wodAgeRangeService->transformToDTO($wodVariant->getWodAgeRange(), $filters) : null,
            rounds   : $wodVariant->getRounds(),
            timeCap  : $wodVariant->getTimeCap(),
            exercises: $wodVariantExerciseDTOs
        );
    }

    public function transformCollectionToDTO(array $wodVariants, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodVariants,
            fn(WodVariant $wodVariant) => $this->transformToDTO($wodVariant, $filters)
        );
    }
}