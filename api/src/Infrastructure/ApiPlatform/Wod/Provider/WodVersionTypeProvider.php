<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Service\WodVersionTypeService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionTypeRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionTypeProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                              $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface        $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface        $collectionProvider,
        private readonly WodVersionTypeService    $wodVersionTypeService,
        private readonly WodVersionTypeRepository $wodVersionTypeRepository
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

    /** @return WodVersionType[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $wodVersionTypes WodVersionType[] */
        $wodVersionTypes = $this->wodVersionTypeRepository->findAll();

        $enriched = [];
        foreach ($wodVersionTypes as $wodVersionType) {
            $enriched[] = $this->wodVersionTypeService->enrichEntity($wodVersionType);
        }

        return $this->paginate($enriched, $operation, $context);
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodVersionTypeId = $uriVariables['id'] ?? null;
        if (!$wodVersionTypeId) {
            throw new ApiException('wod.version_type.required', 400);
        }

        $wodVersionType = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$wodVersionType instanceof WodVersionType) {
            throw new ApiException('wod.version_type.not_found', 404);
        }

        return $this->wodVersionTypeService->enrichEntity($wodVersionType);
    }
}