<?php

namespace App\UI\ApiPlatform\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiPaginationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        $pagination = $request->attributes->get('_api_pagination');
        if (!is_array($pagination)) {
            return;
        }

        switch ($request->getRequestFormat()) {
            case 'json':
                $this->handleJson($response, $pagination);
                break;

            case 'jsonapi':
                $this->handleJsonApi($response, $pagination, $request);
                break;
        }
    }

    private function handleJson($response, array $pagination): void
    {
        $response->headers->set('Pagination-Count', $pagination['totalItems']);
        $response->headers->set('Pagination-Page', $pagination['currentPage']);
        $response->headers->set('Pagination-Per-Page', $pagination['itemsPerPage']);
    }

    private function handleJsonApi($response, array $pagination, $request): void
    {
        $paginationData = $this->formatPagination($pagination);
        $links          = $this->formatLinks($paginationData, $request);

        $content          = json_decode($response->getContent(), true);
        $content['meta']  = $paginationData;
        $content['links'] = $links;

        $response->setContent(json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    private function formatPagination(array $pagination): array
    {
        $totalItems   = $pagination['totalItems'] ?? 0;
        $itemsPerPage = $pagination['itemsPerPage'] ?? 1;
        $currentPage  = $pagination['currentPage'] ?? 1;
        $totalPages   = (int)ceil($totalItems / max(1, $itemsPerPage));

        return [
            'totalItems'   => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'totalPages'   => $totalPages,
        ];
    }

    private function formatLinks(array $pagination, $request): array
    {
        $totalPages  = (int)ceil(($pagination['totalItems'] ?? 0) / max(1, $pagination['itemsPerPage'] ?? 1));
        $currentPage = $pagination['currentPage'] ?? 1;
        $baseUri     = $request->getBaseUrl() . $request->getPathInfo();

        $links = [
            'self'  => $baseUri . '?page=' . $currentPage,
            'first' => $baseUri . '?page=1',
            'last'  => $baseUri . '?page=' . $totalPages,
        ];

        if ($currentPage > 1) {
            $links['prev'] = $baseUri . '?page=' . ($currentPage - 1);
        }
        if ($currentPage < $totalPages) {
            $links['next'] = $baseUri . '?page=' . ($currentPage + 1);
        }

        return $links;
    }
}
