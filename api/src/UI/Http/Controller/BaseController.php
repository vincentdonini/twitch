<?php

namespace App\UI\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SerializerInterface $serializer,
    ) {

    }

//    protected function getPaginationAndSortingParameters(Request $request): array
//    {
//        // Sort and pagination
//        $sortBy    = $request->query->get('sortBy', 'id');
//        $sortOrder = $request->query->get('sortOrder', 'asc');
//        $sort      = $request->query->get('sort'); // Format : field:order,field:order
//        $offset    = (int)$request->query->get('offset', 0);
//        $limit     = (int)$request->query->get('limit', 10);
//
//        // Check if limit is equal to -1 to disable paging
//        if ($limit < 0) {
//            $limit        = PHP_INT_MAX;
//            $displayLimit = -1;
//        } else {
//            $displayLimit = $limit;
//        }
//
//        // Handle the sort parameter (if provided)
//        if ($sort) {
//            $sortFields = explode(',', $sort);
//            foreach ($sortFields as $fieldOrder) {
//                [$field, $order] = explode(':', $fieldOrder);
//                $sortBy    = $field;
//                $sortOrder = $order;
//            }
//        }
//
//        return [
//            'sortBy'       => $sortBy,
//            'sortOrder'    => $sortOrder,
//            'offset'       => $offset,
//            'limit'        => $limit,
//            'displayLimit' => $displayLimit,
//        ];
//    }
//
//    protected function singleResourceJsonResponse(
//        mixed $item,
//        array $serializationGroups = [],
//        int   $statusCode = Response::HTTP_OK,
//    ): JsonResponse {
//        $json = $this->serializer->serialize($item, 'json', [
//            'groups' => $serializationGroups,
//        ]);
//
//        return new JsonResponse($json, $statusCode, [], true);
//    }
//
//    protected function listResourceJsonResponse(
//        array $items,
//        array $serializationGroups = [],
//        int   $statusCode = Response::HTTP_OK,
//    ): JsonResponse {
//        $json = $this->serializer->serialize($items, 'json', [
//            'groups' => $serializationGroups,
//        ]);
//
//        return new JsonResponse($json, $statusCode, [], true);
//    }
//
//    protected function paginatedResourceJsonResponse(
//        Request $request,
//        int     $total,
//        int     $offset,
//        int     $limit,
//        array   $items,
//        array   $serializationGroups = [],
//        int     $statusCode = Response::HTTP_OK,
//    ): JsonResponse {
//        if ($limit === 0 || $limit < -1) {
//            throw new BadRequestHttpException($this->translator->trans('error.pagination.invalid_limit'));
//        }
//
//        if ($offset < 0) {
//            throw new BadRequestHttpException($this->translator->trans('error.pagination.invalid_offset'));
//        }
//
//        $baseUrl     = $request->getSchemeAndHttpHost() . $request->getPathInfo();
//        $queryParams = $request->query->all();
//
//        $json = $this->serializer->serialize($items, 'json', [
//            'groups' => $serializationGroups,
//        ]);
//
//        $headers = [];
//
//        if ($limit != -1) {
//            $headers['X-Total-Count'] = $total;
//            $headers['Content-Range'] = sprintf(
//                'items %d-%d/%d',
//                $offset,
//                min($offset + $limit - 1, $total - 1),
//                $total
//            );
//
//            $buildUrl = function(int $newOffset) use ($baseUrl, $limit, $queryParams): string {
//                $params = array_merge($queryParams, [
//                    'offset' => $newOffset,
//                    'limit'  => $limit
//                ]);
//                return $baseUrl . '?' . http_build_query($params);
//            };
//
//            $links = [];
//
//            if ($offset + $limit < $total) {
//                $links[] = sprintf('<%s>; rel="next"', $buildUrl($offset + $limit));
//            }
//
//            if ($offset > 0) {
//                $prevOffset = max($offset - $limit, 0);
//                $links[] = sprintf('<%s>; rel="prev"', $buildUrl($prevOffset));
//            }
//
//            $links[] = sprintf('<%s>; rel="first"', $buildUrl(0));
//            $lastOffset = $limit > 0 ? floor(($total - 1) / $limit) * $limit : 0;
//            $links[] = sprintf('<%s>; rel="last"', $buildUrl($lastOffset));
//
//            if (!empty($links)) {
//                $headers['Link'] = implode(', ', $links);
//            }
//        }
//
//        return new JsonResponse($json, $statusCode, $headers, true);
//    }
//
//    protected function errorJsonResponse(
//        string $code,
//        int    $status = Response::HTTP_BAD_REQUEST,
//        array  $parameters = []
//    ): JsonResponse {
//        return $this->json([
//            'error' => [
//                'code'    => $code,
//                'message' => $this->translator->trans('error.' . $code, $parameters),
//                'status'  => $status,
//            ],
//        ], $status);
//    }
//
//    protected function getPaginationAndSortingParameters(Request $request): array
//    {
//        $limit     = min((int)$request->query->get('limit', 10), 100);
//        $offset    = (int)$request->query->get('offset', 0);
//        $sortBy    = $request->query->get('sortBy', 'id');
//        $sortOrder = $request->query->get('sortOrder', 'asc');
//
//        return [
//            'limit'     => $limit,
//            'offset'    => $offset,
//            'sortBy'    => $sortBy,
//            'sortOrder' => $sortOrder,
//        ];
//    }

    protected function paginatedResourceJsonResponse(
        Request $request,
        int     $total,
        int     $offset,
        int     $limit,
        array   $items,
        array   $included = [],
        array   $serializationGroups = []
    ): JsonResponse {
        $data = [
            'data'     => $this->serializer->normalize($items, null, ['groups' => $serializationGroups]),
            'included' => $this->serializer->normalize($included, null, ['groups' => $serializationGroups]),
            'meta'     => [
                'total'  => $total,
                'offset' => $offset,
                'limit'  => $limit,
            ],
            'links'    => [
                'self'  => $request->getUri(),
                'first' => $this->buildPaginationLink($request, 0, $limit),
                'last'  => $this->buildPaginationLink($request, max(0, (int)floor(($total - 1) / $limit) * $limit), $limit),
                'next'  => $offset + $limit < $total
                    ? $this->buildPaginationLink($request, $offset + $limit, $limit)
                    : null,
                'prev'  => $offset > 0
                    ? $this->buildPaginationLink($request, max(0, $offset - $limit), $limit)
                    : null,
            ],
        ];

        return new JsonResponse($data, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }

    private function buildPaginationLink(Request $request, int $offset, int $limit): string
    {
        $query           = $request->query->all();
        $query['offset'] = $offset;
        $query['limit']  = $limit;

        return $request->getSchemeAndHttpHost() . $request->getPathInfo() . '?' . http_build_query($query);
    }

    protected function errorJsonResponse(string $title, string $detail, int $status): JsonResponse
    {
        return new JsonResponse([
            'errors' => [
                [
                    'status' => $status,
                    'title'  => $title,
                    'detail' => $detail,
                ],
            ],
        ], $status, [
            'Content-Type' => 'application/vnd.api+json',
        ]);
    }
}
