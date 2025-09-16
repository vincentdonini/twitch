<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Service\WodTypeService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodTypeProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                       $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly WodTypeService    $wodTypeService
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

    /** @return WodType[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodTypes WodType[] */
        $wodTypes = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($wodTypes);

        $enriched = [];
        foreach ($wodTypes as $wodType) {
            $enriched[] = $this->wodTypeService->enrichEntity($wodType);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodTypeId = $uriVariables['id'] ?? null;
        if (!$wodTypeId) {
            throw new ApiException('wod.type.required', 400);
        }

        $wodType = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodType instanceof WodType) {
            throw new ApiException('wod.type.not_found', 404);
        }

        return $this->wodTypeService->enrichEntity($wodType);
    }
}