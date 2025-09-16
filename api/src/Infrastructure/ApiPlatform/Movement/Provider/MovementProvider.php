<?php

namespace App\Infrastructure\ApiPlatform\Movement\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Movement\Entity\Movement;
use App\Domain\Movement\Service\MovementService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MovementProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                       $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly MovementService   $movementService
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
        /** @var $movements Movement[] */
        $movements = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($movements);

        $enriched = [];
        foreach ($movements as $movement) {
            $enriched[] = $this->movementService->enrichEntity($movement);
        }

        return $enriched;
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