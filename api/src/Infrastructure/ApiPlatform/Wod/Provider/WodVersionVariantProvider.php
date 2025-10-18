<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Service\WodVersionVariantService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionVariantRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionVariantProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                                 $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface           $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface           $collectionProvider,
        private readonly WodVersionVariantService    $wodVersionVariantService,
        private readonly WodVersionVariantRepository $wodVersionVariantRepository
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

    /** @return WodVersionVariant[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodVersionVariants WodVersionVariant[] */
        $wodVersionVariants = $this->wodVersionVariantRepository->findAll();

        $enriched = [];
        foreach ($wodVersionVariants as $wodVersionVariant) {
            $enriched[] = $this->wodVersionVariantService->enrichEntity($wodVersionVariant);
        }

        return $this->paginate($enriched, $operation, $context);
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodVersionVariantId = $uriVariables['id'] ?? null;
        if (!$wodVersionVariantId) {
            throw new ApiException('wod.version_variant.required', 400);
        }

        $wodVersionVariant = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodVersionVariant instanceof WodVersionVariant) {
            throw new ApiException('wod.version_variant.not_found', 404);
        }

        return $this->wodVersionVariantService->enrichEntity($wodVersionVariant);
    }
}