<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Service\WodVersionService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                       $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly WodVersionService $wodVersionService
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

    /** @return WodVersion[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodVersions WodVersion[] */
        $wodVersions = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($wodVersions);

        $enriched = [];
        foreach ($wodVersions as $wodVersion) {
            $enriched[] = $this->wodVersionService->enrichEntity($wodVersion);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodVersionId = $uriVariables['id'] ?? null;
        if (!$wodVersionId) {
            throw new ApiException('wod.version.required', 400);
        }

        $wodVersion = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodVersion instanceof WodVersion) {
            throw new ApiException('wod.version.not_found', 404);
        }

        return $this->wodVersionService->enrichEntity($wodVersion);
    }
}