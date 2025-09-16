<?php

namespace App\Infrastructure\ApiPlatform\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractProvider implements ProviderInterface
{
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

    protected function handlePagination($paginator): void
    {
        $totalItems   = $paginator->getTotalItems();
        $itemsPerPage = $paginator->getItemsPerPage();
        $currentPage  = $paginator->getCurrentPage();
        $totalPages   = (int)ceil($totalItems / max(1, $itemsPerPage));

        $pagination = [
            'totalItems'   => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'totalPages'   => $totalPages,
        ];

        $request = $this->requestStack->getCurrentRequest();
        $request?->attributes->set('_api_pagination', $pagination);
    }

    protected function getItemOrThrow(Operation $operation, array $uriVariables, array $context, string $entityClass): object
    {
        $item = $this->getItemProvider()->provide($operation, $uriVariables, $context);

        if (!$item instanceof $entityClass) {
            throw new ApiException(strtolower((new ReflectionClass($entityClass))->getShortName()) . '.not_found', 404);
        }

        return $item;
    }
}
