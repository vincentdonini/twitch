<?php

namespace App\UI\Controller\Muscle;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Domain\Muscle\Filters\MuscleSegmentFilterMapping;
use App\Domain\Muscle\Filters\MuscleSegmentFilterRules;
use App\Domain\Muscle\MuscleSegment\GetMuscleSegmentByIdUseCase;
use App\Domain\Muscle\MuscleSegment\GetMuscleSegmentContentsByIdUseCase;
use App\Domain\Muscle\MuscleSegment\GetMuscleSegmentsByMuscleIdUseCase;
use App\Domain\Muscle\MuscleSegment\ListMuscleSegmentsUseCase;
use App\Domain\Muscle\MuscleSegment\UpsertContentMuscleSegmentBulkUseCase;
use App\Domain\Muscle\MuscleSegment\UpsertContentMuscleSegmentUseCase;
use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use App\Domain\Muscle\Service\MuscleSegmentService;
use App\Domain\Muscle\Sort\MuscleSegmentSortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Muscle\MuscleSegment\UpsertContentMuscleSegmentBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleSegment\UpsertContentMuscleSegmentHttp;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(path: '/muscle-segments', name: 'muscle_segment_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final readonly class MuscleSegmentController
{
    use ApiExceptionHandler;

    public function __construct(
        private MuscleSegmentService      $muscleSegmentService,
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of Muscle segments available in the system.',
        summary    : 'List of Muscle segments.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by Muscle segment slug (exact match)',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[muscle.id][eq]',
                description: 'Filter by parent Muscle ID (exact match)',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[title][eq]',
                description: 'Filter by Muscle segment title (exact match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[title][like]',
                description: 'Filter by Muscle segment title (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[summary][like]',
                description: 'Filter by Muscle segment summary (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of muscle segments.',
                headers    : [
                    new OAT\Header(ref: '#/components/headers/Element-Count', header: 'Element-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Page', header: 'Pagination-Page'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Count', header: 'Pagination-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Limit', header: 'Pagination-Limit'),
                ],
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : MuscleSegment::class,
                            groups: [FrontGroupsEnum::MUSCLE_SEGMENT_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                    $request,
        ListMuscleSegmentsUseCase  $useCase,
        NormalizerInterface        $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(request: $request);

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : MuscleSegmentFilterMapping::FIELD_MAP,
            allowedOperators: MuscleSegmentFilterRules::ALLOWED_OPERATORS,
            allowedFields   : MuscleSegmentFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : MuscleSegmentSortMapping::FIELD_MAP,
            defaultSort: 'slug'
        );

        try {
            $paginator = $useCase->execute(
                page   : $paginatorValues->getPage(),
                limit  : $paginatorValues->getLimit(),
                filters: $filters,
                sorts  : $sorts,
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->muscleSegmentService->transformCollectionToDTO(
            $paginator->getItems(),
            $filters,
            $this->muscleSegmentDAL->getWodCounts(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: ['groups' => [FrontGroupsEnum::MUSCLE_SEGMENT_LIST]]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{segmentId}',
        name        : 'detail',
        requirements: ['segmentId' => '[0-9a-fA-F\-]+'],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific Muscle segment.',
        summary    : 'Get Muscle segment details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Muscle segment details.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : MuscleSegment::class,
                        groups: [FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ]
    )]
    public function detail(
        GetMuscleSegmentByIdUseCase $useCase,
        NormalizerInterface         $normalizer,
        string                      $segmentId
    ): JsonResponse {
        try {
            $segment = $useCase->execute(Uuid::fromString($segmentId));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->muscleSegmentService->transformToDTO($segment);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: ['groups' => [FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL]]
            ),
            status: Response::HTTP_OK,
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // BY MUSCLE
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/by-muscle/{muscleId}',
        name        : 'by_muscle',
        requirements: ['muscleId' => '[0-9a-fA-F\-]+'],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all segments for a given Muscle.',
        summary    : 'List of Muscle segments by Muscle.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of muscle segments for the given muscle.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : MuscleSegment::class,
                            groups: [FrontGroupsEnum::MUSCLE_SEGMENT_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function byMuscle(
        GetMuscleSegmentsByMuscleIdUseCase $useCase,
        NormalizerInterface                $normalizer,
        string                             $muscleId
    ): JsonResponse {
        try {
            $segments = $useCase->execute(Uuid::fromString($muscleId));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->muscleSegmentService->transformCollectionToDTO($segments);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: ['groups' => [FrontGroupsEnum::MUSCLE_SEGMENT_LIST]]
            ),
            status: Response::HTTP_OK,
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{segmentId}/contents',
        name        : 'contents_list',
        requirements: ['segmentId' => '[0-9a-fA-F\-]+'],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_SEGMENT_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for a Muscle segment.',
        summary    : 'List of all Muscle segment contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all Muscle segment contents.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : 'object',
                            groups: [FrontGroupsEnum::CONTENT_MUSCLE_SEGMENT_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function listContents(
        string                              $segmentId,
        NormalizerInterface                 $normalizer,
        GetMuscleSegmentContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $contents = $useCase->execute(Uuid::fromString($segmentId));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $contentsArray = [];
        foreach ($contents as $content) {
            $contentsArray[$content->getLocale()] = [
                'title'   => $content->getTitle(),
                'summary' => $content->getSummary(),
                'details' => $content->getDetails(),
            ];
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $contentsArray,
                format : 'json',
                context: ['groups' => [FrontGroupsEnum::CONTENT_MUSCLE_SEGMENT_LIST]]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{segmentId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'segmentId' => '[0-9a-fA-F\-]+',
            'locale'    => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_SEGMENT_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update Muscle segment content for a given locale.',
        summary    : 'Upsert Muscle segment localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['title', 'summary'],
                properties: [
                    new OAT\Property(property: 'title', type: 'string', example: 'Lorem ipsum'),
                    new OAT\Property(property: 'summary', type: 'string', example: 'Lorem ipsum dolor sit amet.'),
                    new OAT\Property(property: 'details', type: 'string', nullable: true),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: Response::HTTP_NO_CONTENT, description: 'Content saved.'),
            new OAT\Response(response: Response::HTTP_BAD_REQUEST, description: 'Invalid payload.'),
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'Muscle segment not found.'),
        ]
    )]
    public function upsertContent(
        string                            $segmentId,
        string                            $locale,
        Request                           $request,
        UpsertContentMuscleSegmentUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleSegmentHttp(
                    id     : Uuid::fromString($segmentId),
                    locale : $locale,
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{segmentId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: ['segmentId' => '[0-9a-fA-F\-]+'],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_SEGMENT_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for a Muscle segment.',
        summary    : 'Upsert multiple Muscle segment contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => ["title" => "Lorem ipsum", "summary" => "Lorem ipsum dolor sit amet.", "details" => null],
                    "en" => ["title" => "Lorem ipsum", "summary" => "Lorem ipsum dolor sit amet.", "details" => null],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: Response::HTTP_NO_CONTENT, description: 'Contents saved.'),
            new OAT\Response(response: Response::HTTP_BAD_REQUEST, description: 'Invalid payload.'),
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'Muscle segment not found.'),
        ]
    )]
    public function upsertContentsBulk(
        string                                $segmentId,
        Request                               $request,
        UpsertContentMuscleSegmentBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleSegmentBulkHttp(
                    id     : Uuid::fromString($segmentId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
