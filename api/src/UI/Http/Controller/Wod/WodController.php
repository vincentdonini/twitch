<?php

namespace App\UI\Http\Controller\Wod;

use App\Application\Serializer\JsonApi\IncludedCollector;
use App\Application\Serializer\JsonApi\JsonApiQueryParser;
use App\Domain\Wod\Repository\WodRepositoryInterface;
use App\Domain\Wod\Service\WodService;
use App\UI\Http\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
#[Route('/wods', name: 'wod_')]
final class WodController extends BaseController
{
    public function __construct(
        private readonly WodRepositoryInterface $wodRepository,
        private readonly WodService             $wodService,
        private readonly JsonApiQueryParser     $queryParser,
        protected TranslatorInterface           $translator,
        protected SerializerInterface           $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get WOD list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'WOD list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(Request $request): JsonResponse
    {
        // On parse la requête via JSON:API
        $query = $this->queryParser->parse($request);

        // On récupère les WODs avec filtres et pagination
        $wods = $this->wodRepository->findByFilters(
            sortBy   : $query->sorting->field,
            sortOrder: $query->sorting->order,
            offset   : $query->pagination->offset,
            limit    : $query->pagination->limit,
            search   : $query->search,
            filters  : $query->filters
        );

        $total = $this->wodRepository->countByFilters(
            search : $query->search,
            filters: $query->filters
        );

        $includedCollector = new IncludedCollector($this->serializer);

        // Transformation en JSON:API resources
        $resources = [];
        foreach ($wods as $wod) {
            $resources[] = $this->wodService->transformToResourceDTO($wod, $includedCollector, $query->includes);
        }

        return $this->paginatedResourceJsonResponse(
            request            : $request,
            total              : $total,
            offset             : $query->pagination->offset,
            limit              : $query->pagination->limit,
            items              : $resources,
            included           : $includedCollector->getIncluded(),
            serializationGroups: ['wod:list', 'wod:detail']
        );
    }

//    #[Route('/{wodId}', name: 'detail', requirements: ['wodId' => '\d+'], methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    #[OA\Get(
//        summary  : 'Get WOD details by ID',
//        security : [['bearerAuth' => []]],
//        responses: [
//            new OA\Response(response: 200, description: 'Get WOD details'),
//            new OA\Response(response: 403, description: 'Access denied'),
//            new OA\Response(response: 404, description: 'WOD not found'),
//        ]
//    )]
//    #[ApiSecurity(name: 'bearerAuth')]
//    public function detail(string $wodId): JsonResponse
//    {
//        $wod = $this->wodRepository->findOneById($wodId);
//        if (!$wod) {
//            return $this->errorJsonResponse(
//                'wod.not_found',
//                Response::HTTP_NOT_FOUND,
//            );
//        }
//
//        return $this->singleResourceJsonResponse(
//            item               : $this->wodService->transformToResource($wod),
//            serializationGroups: ['wod:detail']
//        );
//    }

//    #[Route('/{wodId}/versions', name: 'versions', requirements: ['wodId' => '\d+'], methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    #[OA\Get(
//        summary  : 'Get versions for a specific WOD',
//        security : [['bearerAuth' => []]],
//        responses: [
//            new OA\Response(response: 200, description: 'List of versions for the WOD'),
//            new OA\Response(response: 403, description: 'Access denied'),
//            new OA\Response(response: 404, description: 'WOD not found'),
//        ]
//    )]
//    #[ApiSecurity(name: 'bearerAuth')]
//    public function listVersions(int $wodId, Request $request): JsonResponse
//    {
//        $wod = $this->wodRepository->findOneById($wodId);
//        if (!$wod) {
//            return $this->errorJsonResponse(
//                'wod.not_found',
//                Response::HTTP_NOT_FOUND,
//            );
//        }
//
//        $params  = $this->getPaginationAndSortingParameters($request);
//        $search  = $request->query->get('search') ?: null;
//        $types   = $request->query->get('types');
//        $filters = [
//            'wodId'   => $wodId,
//            'typeIds' => $types ? array_map('intval', explode(',', $types)) : [],
//        ];
//
//        $wodVersions = $this->wodVersionRepository->findByFilters(
//            sortBy   : $params['sortBy'],
//            sortOrder: $params['sortOrder'],
//            limit    : $params['limit'],
//            search   : $search,
//            filters  : $filters,
//        );
//
//        return $this->paginatedResourceJsonResponse(
//            request            : $request,
//            total              : $this->wodVersionRepository->countByFilters($search, $filters),
//            offset             : $params['offset'],
//            limit              : $params['displayLimit'],
//            items              : array_map(
//                fn($wodVersion) => $this->wodVersionService->transformToDTO($wodVersion),
//                $wodVersions
//            ),
//            serializationGroups: ['wodVersion:list']
//        );
//    }
}
