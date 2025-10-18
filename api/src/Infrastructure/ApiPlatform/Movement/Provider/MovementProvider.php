<?php

namespace App\Infrastructure\ApiPlatform\Movement\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Movement\Entity\Movement;
use App\Domain\Movement\Service\MovementService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use App\Infrastructure\Persistence\Doctrine\Movement\MovementRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MovementProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                        $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface  $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface  $collectionProvider,
        private readonly MovementService    $movementService,
        private readonly MovementRepository $movementRepository
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

    /** @return Movement[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var Movement[] $movements */
        $movements = $this->movementRepository->findAll();

        $enriched = [];
        foreach ($movements as $movement) {
            $enriched[] = $this->movementService->enrichEntity($movement);
        }

        return $this->paginate($enriched, $operation, $context);
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $movementId = $uriVariables['id'] ?? null;
        if (!$movementId) {
            throw new ApiException('movement.required', 400);
        }

        $movement = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$movement instanceof Movement) {
            throw new ApiException('movement.not_found', 404);
        }

        return $this->movementService->enrichEntity($movement);
    }
}