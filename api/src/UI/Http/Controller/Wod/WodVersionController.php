<?php

namespace App\UI\Http\Controller\Wod;

use App\Domain\Wod\Repository\WodVersionRepositoryInterface;
use App\Domain\Wod\Service\WodVersionService;
use App\UI\Http\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
#[Route('/wod-versions', name: 'wod_version_')]
final class WodVersionController extends BaseController
{
    public function __construct(
        private readonly WodVersionRepositoryInterface $wodVersionRepository,
        private readonly WodVersionService             $wodVersionService,
        protected TranslatorInterface                  $translator,
        protected SerializerInterface                  $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get WOD version list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'WOD version list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(Request $request): JsonResponse
    {
//        $params               = $this->getPaginationAndSortingParameters($request);
//        $search               = $request->query->get('search');
//        $wod                  = $request->query->get('wod');
//        $types                = $request->query->get('types');
//        $durationMax          = $request->query->get('durationMax');
//        $allowedEquipments    = $request->query->get('allowedEquipments');
//        $disallowedEquipments = $request->query->get('disallowedEquipments');
//        $allowedExercises     = $request->query->get('allowedExercises');
//        $disallowedExercises  = $request->query->get('disallowedExercises');
//
//        $filters = [
//            'wodId'                  => $wod ? array_map('intval', explode(',', $wod)) : [],
//            'typeIds'                => $types ? array_map('intval', explode(',', $types)) : [],
//            'durationMax'            => isset($durationMax) && is_numeric($durationMax) ? (int)$durationMax : null,
//            'allowedEquipmentIds'    => $allowedEquipments ? array_map('intval', explode(',', $allowedEquipments)) : [],
//            'disallowedEquipmentIds' => $disallowedEquipments ? array_map('intval', explode(',', $disallowedEquipments)) : [],
//            'allowedExerciseIds'     => $allowedExercises ? array_map('intval', explode(',', $allowedExercises)) : [],
//            'disallowedExerciseIds'  => $disallowedExercises ? array_map('intval', explode(',', $disallowedExercises)) : [],
//        ];
//
//        $wodVersions = $this->wodVersionRepository->findByFilters(
//            sortBy   : $params['sortBy'],
//            sortOrder: $params['sortOrder'],
//            offset   : $params['offset'],
//            limit    : $params['limit'],
//            search   : $search ?: null,
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
    }

    #[Route('/{wodVersionId}', name: 'detail', requirements: ['wodVersionId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        summary  : 'Get WOD version details by ID',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Get WOD version details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'WOD version not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $wodVersionId): JsonResponse
    {
//        $wodVersion = $this->wodVersionRepository->findOneById($wodVersionId);
//        if (!$wodVersion) {
//            return $this->errorJsonResponse(
//                'wod.version.not_found',
//                Response::HTTP_NOT_FOUND,
//            );
//        }
//
//        return $this->singleResourceJsonResponse(
//            item               : $this->wodVersionService->transformToDTO($wodVersion),
//            serializationGroups: ['wodVersion:detail']
//        );
    }
}
