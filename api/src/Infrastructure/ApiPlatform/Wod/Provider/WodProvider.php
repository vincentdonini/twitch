<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Service\WodService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                       $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly WodService        $wodService
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

    /** @return Wod[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        $filters = $context['filters'] ?? [];

        /** @var $wods Wod[] */
        $wods = $this->getCollectionProvider()->provide($operation, $uriVariables, $context);
        $this->handlePagination($wods);

        $enriched = [];
        foreach ($wods as $wod) {
            $this->applyFilters($wod, $filters);
            $enriched[] = $this->wodService->enrichEntity($wod);
        }

        return $enriched;
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodId = $uriVariables['id'] ?? null;
        if (!$wodId) {
            throw new ApiException('wod.required', 400);
        }

        /** @var Wod|null $wod */
        $wod = $this->getItemProvider()->provide($operation, $uriVariables, $context);
        if (!$wod instanceof Wod) {
            throw new ApiException('wod.not_found', 404);
        }

        return $this->wodService->enrichEntity($wod);
    }

    private function applyFilters(Wod $wod, array $filters): void
    {
        if (isset($filters['wodVersions.wodVersionVariants.gender'])) {
            $genderFilter = $filters['wodVersions.wodVersionVariants.gender'];
            foreach ($wod->getWodVersions() as $version) {
                $variants = $version->getWodVersionVariants();

                $filteredVariants = $variants->filter(function ($variant) use ($genderFilter) {
                    return $variant->getGender()?->value === $genderFilter;
                });

                $version->setWodVersionVariants($filteredVariants);
            }
        }
    }
}
