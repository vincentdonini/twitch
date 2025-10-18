<?php

namespace App\Infrastructure\ApiPlatform\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractProvider implements ProviderInterface
{
    // TODO : paginator (common) ???
    public function __construct(
        protected RequestStack $requestStack,
    ) {
    }

    abstract protected function provideCollection(Operation $operation, array $uriVariables, array $context): array;

    abstract protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed;

    abstract protected function getItemProvider(): ProviderInterface;

    abstract protected function getCollectionProvider(): ProviderInterface;

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->provideCollection($operation, $uriVariables, $context);
        }

        return $this->provideItem($operation, $uriVariables, $context);
    }

    protected function paginate(array $items, Operation $operation, array $context = []): array
    {
        $filters = $context['filters'] ?? [];

        if ($this->isPaginationDisabled($filters, $operation)) {
            return $items;
        }

        [$page, $itemsPerPage] = $this->resolvePaginationParams($filters, $operation);
        $paginator = $this->createPaginator($items, $page, $itemsPerPage);
        $this->updateRequestPagination($paginator, $page, $itemsPerPage);

        return $paginator->getCurrentPageResults();
    }

    private function isPaginationDisabled(array $filters, Operation $operation): bool
    {
        return isset($filters['pagination']) && $filters['pagination'] === 'false'
            || !$operation->getPaginationEnabled();
    }

    private function resolvePaginationParams(array $filters, Operation $operation): array
    {
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;

        $itemsPerPage = $operation->getPaginationItemsPerPage();
        if (isset($filters['itemsPerPage'])) {
            $requested    = (int)$filters['itemsPerPage'];
            $max          = $operation->getPaginationMaximumItemsPerPage() ?? $itemsPerPage;
            $itemsPerPage = min($requested, $max);
        }

        return [$page, $itemsPerPage];
    }

    private function createPaginator(array $items, int $page, int $itemsPerPage): Pagerfanta
    {
        $adapter   = new ArrayAdapter($items);
        $paginator = new Pagerfanta($adapter);
        $paginator
            ->setCurrentPage($page)
            ->setMaxPerPage($itemsPerPage);

        return $paginator;
    }

    private function updateRequestPagination(Pagerfanta $paginator, int $page, int $itemsPerPage): void
    {
        $totalItems = $paginator->getNbResults();
        $totalPages = (int)ceil($totalItems / max(1, $itemsPerPage));

        $pagination = [
            'totalItems'   => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
        ];

        $request = $this->requestStack->getCurrentRequest();
        $request?->attributes->set('_api_pagination', $pagination);
    }
}
