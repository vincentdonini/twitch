<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Service\WodCategoryService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodCategoryProvider extends AbstractProvider
{
    public function __construct(
        RequestStack $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly WodCategoryService $wodCategoryService
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

    /** @return WodCategory[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodCategories WodCategory[] */
        $wodCategories = $this->collectionProvider->provide($operation, $uriVariables, $context);
        $this->handlePagination($wodCategories);

        $enriched = [];
        foreach ($wodCategories as $wodCategory) {
            $enriched[] = $this->wodCategoryService->enrichEntity($wodCategory);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodCategoryId = $uriVariables['id'] ?? null;
        if (!$wodCategoryId) {
            throw new ApiException('wod.category.required', 400);
        }

        $wodCategory = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodCategory instanceof WodCategory) {
            throw new ApiException('wod.category.not_found', 404);
        }

        return $this->wodCategoryService->enrichEntity($wodCategory);
    }
}
