<?php

namespace App\Domain\Wod\Service;

use App\Application\Serializer\JsonApi\IncludedCollector;
use App\Application\Serializer\JsonApi\JsonApiIncludeHelper;
use App\Application\Wod\DTO\WodDTO;
use App\Domain\Equipment\Service\EquipmentService;
use App\Domain\Exercise\Service\ExerciseCategoryService;
use App\Domain\Exercise\Service\ExerciseService;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Entity\WodVersionVariantExercise;
use App\Shared\Utils\LocalizationHelper;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodService
{
    private string $locale;

    public function __construct(
        private readonly WodTypeService                   $wodTypeService,
        private readonly WodVersionService                $wodVersionService,
        private readonly WodVersionTypeService            $wodVersionTypeService,
        private readonly WodVersionVariantService         $wodVersionVariantService,
        private readonly WodCategoryService               $wodCategoryService,
        private readonly WodVersionVariantExerciseService $wodVersionVariantExerciseService,
        private readonly ExerciseService                  $exerciseService,
        private readonly ExerciseCategoryService          $exerciseCategoryService,
        private readonly EquipmentService                 $equipmentService,
        private readonly RequestStack                     $requestStack,
    ) {
        $this->locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';
    }

    public function transformToResourceDTO(Wod $wod, IncludedCollector $collector, array $includes = []): WodDTO
    {
        $includeTree = JsonApiIncludeHelper::parse($includes);

        $dto = new WodDTO(
            id           : $wod->getId(),
            title        : $wod->getTitle(),
            description  : LocalizationHelper::getLocalizedValue($wod->getDescription(), $this->locale),
            wodTypeId    : $wod->getWodType()->getId(),
            wodCategoryId: $wod->getWodCategory()->getId(),
        );

        // --- Includes principaux ---
        $this->handleWodIncludes($wod, $includeTree, $collector);

        // --- WodVersions et sous-relations ---
        foreach ($wod->getWodVersions() as $version) {
            $this->handleWodVersionIncludes($version, $includeTree, $collector);
        }

        return $dto;
    }

    private function handleWodIncludes(Wod $wod, array $tree, IncludedCollector $collector): void
    {
        JsonApiIncludeHelper::addIfIncluded(
            'wodTypes', $tree, $collector,
            fn() => $this->wodTypeService->transformToDTO($wod->getWodType()),
            ['wod:list', 'wod:detail']
        );

        JsonApiIncludeHelper::addIfIncluded(
            'wodCategories', $tree, $collector,
            fn() => $this->wodCategoryService->transformToDTO($wod->getWodCategory()),
            ['wod:list', 'wod:detail']
        );
    }

    private function handleWodVersionIncludes(WodVersion $version, array $tree, IncludedCollector $collector): void
    {
        JsonApiIncludeHelper::addIfIncluded(
            'wodVersions', $tree, $collector,
            fn() => $this->wodVersionService->transformToDTO($version),
            ['wod:list', 'wod:detail']
        );

        $versionTree = JsonApiIncludeHelper::subtree($tree, 'wodVersions');

        JsonApiIncludeHelper::addIfIncluded(
            'wodVersionTypes', $versionTree, $collector,
            fn() => $this->wodVersionTypeService->transformToDTO($version->getWodVersionType()),
            ['wod:list', 'wod:detail']
        );

        foreach ($version->getWodVersionVariants() as $variant) {
            $this->handleWodVersionVariantIncludes($variant, $versionTree, $collector);
        }
    }

    private function handleWodVersionVariantIncludes(WodVersionVariant $variant, array $tree, IncludedCollector $collector): void
    {
        JsonApiIncludeHelper::addIfIncluded(
            'wodVersionVariants', $tree, $collector,
            fn() => $this->wodVersionVariantService->transformToDTO($variant),
            ['wod:list', 'wod:detail']
        );

        $variantTree = JsonApiIncludeHelper::subtree($tree, 'wodVersionVariants');

        foreach ($variant->getWodVersionVariantExercises() as $exerciseRelation) {
            $this->handleExerciseIncludes($exerciseRelation, $variantTree, $collector);
        }
    }

    private function handleExerciseIncludes(WodVersionVariantExercise $exerciseRelation, array $tree, IncludedCollector $collector): void
    {
        JsonApiIncludeHelper::addIfIncluded(
            'wodVersionVariantExercises', $tree, $collector,
            fn() => $this->wodVersionVariantExerciseService->transformToDTO($exerciseRelation),
            ['wod:list', 'wod:detail']
        );

        $exerciseTree = JsonApiIncludeHelper::subtree($tree, 'wodVersionVariantExercises');

        if (JsonApiIncludeHelper::has($exerciseTree, 'exercises')) {
            $exercise        = $exerciseRelation->getExercise();
            $exerciseSubTree = JsonApiIncludeHelper::subtree($exerciseTree, 'exercises');

            $collector->add(
                'exercises',
                $this->exerciseService->transformToDTO($exercise),
                ['wod:list', 'wod:detail']
            );

            JsonApiIncludeHelper::addIfIncluded(
                'equipments', $exerciseSubTree, $collector,
                fn() => $this->equipmentService->transformToDTO($exercise->getEquipment()),
                ['wod:list', 'wod:detail']
            );

            JsonApiIncludeHelper::addIfIncluded(
                'exerciseCategories', $exerciseSubTree, $collector,
                fn() => $this->exerciseCategoryService->transformToDTO($exercise->getExerciseCategory()),
                ['wod:list', 'wod:detail']
            );
        }
    }
}
