<?php

namespace App\Infrastructure\ApiPlatform\Muscle\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Service\MuscleAreaService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MuscleAreaProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly MuscleAreaService $muscleAreaService
    ) {
        parent::__construct($requestStack);
    }

    protected function getItemProvider(): ProviderInterface
    {
        return $this->itemProvider;
    }

    protected function getCollectionProvider(): ProviderInterface
    {
        return $this->collectionProvider;
    }

    /** @return MuscleArea[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $muscleAreas MuscleArea[]  */
        $muscleAreas = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($muscleAreas);

        $enriched = [];
        foreach ($muscleAreas as $muscleArea) {
            $enriched[] = $this->muscleAreaService->enrichEntity($muscleArea);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): MuscleArea
    {
        $muscleAreaId = $uriVariables['id'] ?? null;
        if (!$muscleAreaId) {
            throw new ApiException('muscle.area.required', 400);
        }

        $muscleArea = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$muscleArea instanceof MuscleArea) {
            throw new ApiException('muscle.area.not_found', 404);
        }

        return $this->muscleAreaService->enrichEntity($muscleArea);
    }
}