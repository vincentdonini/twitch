<?php

namespace App\Infrastructure\ApiPlatform\Muscle\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Service\MuscleGroupService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MuscleAreaGroupProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly EntityManagerInterface $em,
        private readonly MuscleGroupService     $muscleGroupService
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
        // Check required parameters
        // -------------------------------------------------------------------------------------------------------------
        $areaId = $uriVariables['areaId'] ?? null;
        if (!$areaId) {
            throw new ApiException('muscle.area.required', 400);
        }

        // Fetch and validate AREA
        // -------------------------------------------------------------------------------------------------------------
        $area = $this->em->getRepository(MuscleArea::class)->find($areaId);
        if (!$area) {
            throw new ApiException('muscle.area.not_found', 404);
        }

        // Collection
        // -------------------------------------------------------------------------------------------------------------
        $groups = $this->em
            ->getRepository(MuscleGroup::class)
            ->findBy(['area' => $areaId]);

        return array_map(
            fn(MuscleGroup $group) => $this->muscleGroupService->enrichEntity($group),
            $groups
        );
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): MuscleGroup
    {
        // Check required parameters
        // -------------------------------------------------------------------------------------------------------------
        $areaId = $uriVariables['areaId'] ?? null;
        if (!$areaId) {
            throw new ApiException('muscle.area.required', 400);
        }

        $groupId = $uriVariables['groupId'] ?? null;
        if (!$groupId) {
            throw new ApiException('muscle.group.required', 400);
        }

        // Fetch and validate AREA
        // -------------------------------------------------------------------------------------------------------------
        $area = $this->em->getRepository(MuscleArea::class)->find($areaId);
        if (!$area) {
            throw new ApiException('muscle.area.not_found', 404);
        }

        // Item
        // -------------------------------------------------------------------------------------------------------------
        $group = $this->em
            ->getRepository(MuscleGroup::class)
            ->findOneBy(['id' => $groupId, 'area' => $areaId]);
        if (!$group instanceof MuscleGroup) {
            throw new ApiException('muscle.group.not_found', 404);
        }

        return $this->muscleGroupService->enrichEntity($group);
    }
}
