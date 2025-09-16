<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodVersionVariantMovement;
use App\Domain\Wod\Service\WodVersionVariantMovementService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionVariantMovementProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                                      $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface                $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface                $collectionProvider,
        private readonly WodVersionVariantMovementService $wodVersionVariantMovementService
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

    /** @return WodVersionVariantMovement[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodVersionVariantMovements WodVersionVariantMovement[] */
        $wodVersionVariantMovements = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($wodVersionVariantMovements);

        $enriched = [];
        foreach ($wodVersionVariantMovements as $wodVersionVariantMovement) {
            $enriched[] = $this->wodVersionVariantMovementService->enrichEntity($wodVersionVariantMovement);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodVersionVariantMovementId = $uriVariables['id'] ?? null;
        if (!$wodVersionVariantMovementId) {
            throw new ApiException('wod.version_variant_movement.required', 400);
        }

        $wodVersionVariantMovement = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodVersionVariantMovement instanceof WodVersionVariantMovement) {
            throw new ApiException('wod.version_variant_movement.not_found', 404);
        }

        return $this->wodVersionVariantMovementService->enrichEntity($wodVersionVariantMovement);
    }
}