<?php

namespace App\Infrastructure\ApiPlatform\Muscle\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Service\MuscleService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MuscleProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly MuscleService     $muscleService
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
        /** @var $muscles Muscle[] */
        $muscles = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($muscles);

        $enriched = [];
        foreach ($muscles as $muscle) {
            $enriched[] = $this->muscleService->enrichEntity($muscle);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): Muscle
    {
        $muscleId = $uriVariables['id'] ?? null;
        if (!$muscleId) {
            throw new ApiException('muscle.required', 400);
        }

        $muscle = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$muscle instanceof Muscle) {
            throw new ApiException('muscle.not_found', 404);
        }

        return $this->muscleService->enrichEntity($muscle);
    }
}