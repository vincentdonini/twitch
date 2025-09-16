<?php

namespace App\Infrastructure\ApiPlatform\Muscle\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Service\MuscleService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MuscleGroupMusclesProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly EntityManagerInterface $em,
        private readonly MuscleService $muscleService
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

    /** @return Muscle[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        // Check required parameters
        // -------------------------------------------------------------------------------------------------------------
        $groupId = $uriVariables['id'] ?? null;
        if (!$groupId) {
            throw new ApiException('muscle.group.required', 400);
        }

        // Fetch and validate GROUP
        // -------------------------------------------------------------------------------------------------------------
        $group = $this->em->getRepository(MuscleGroup::class)->find($groupId);
        if (!$group) {
            throw new ApiException('muscle.group.not_found', 404);
        }

        // Collection
        // -------------------------------------------------------------------------------------------------------------
        $muscles = $this->em
            ->getRepository(Muscle::class)
            ->findBy(['group' => $groupId]);

        return array_map(fn(Muscle $muscle) => $this->muscleService->enrichEntity($muscle), $muscles);
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        return null;
    }
}
