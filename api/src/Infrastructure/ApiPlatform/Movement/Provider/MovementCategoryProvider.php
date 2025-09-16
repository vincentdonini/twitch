<?php

namespace App\Infrastructure\ApiPlatform\Movement\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Movement\Entity\MovementCategory;
use App\Domain\Movement\Service\MovementCategoryService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MovementCategoryProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                             $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface       $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface       $collectionProvider,
        private readonly MovementCategoryService $movementCategoryService
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

    /** @return MovementCategory[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $movementCategories MovementCategory[] */
        $movementCategories = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($movementCategories);

        $enriched = [];
        foreach ($movementCategories as $movementCategory) {
            $enriched[] = $this->movementCategoryService->enrichEntity($movementCategory);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $movementCategoryId = $uriVariables['id'] ?? null;
        if (!$movementCategoryId) {
            throw new ApiException('movement.category.required', 400);
        }

        $movementCategory = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$movementCategory instanceof MovementCategory) {
            throw new ApiException('movement.category.not_found', 404);
        }

        return $this->movementCategoryService->enrichEntity($movementCategory);
    }
}