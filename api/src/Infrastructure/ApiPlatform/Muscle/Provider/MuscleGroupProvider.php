<?php

namespace App\Infrastructure\ApiPlatform\Muscle\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Service\MuscleGroupService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MuscleGroupProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly MuscleGroupService $muscleGroupService
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

    /** @return MuscleGroup[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $muscleGroups MuscleGroup[] */
        $muscleGroups = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($muscleGroups);

        $enriched = [];
        foreach ($muscleGroups as $muscleGroup) {
            $enriched[] = $this->muscleGroupService->enrichEntity($muscleGroup);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): MuscleGroup
    {
        $muscleGroupId = $uriVariables['id'] ?? null;
        if (!$muscleGroupId) {
            throw new ApiException('muscle.group.required', 400);
        }

        $muscleGroup = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$muscleGroup instanceof MuscleGroup) {
            throw new ApiException('muscle.group.not_found', 404);
        }

        return $this->muscleGroupService->enrichEntity($muscleGroup);
    }
}