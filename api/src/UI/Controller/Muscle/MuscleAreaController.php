<?php

namespace App\UI\Controller\Muscle;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Filters\MuscleAreaFilterMapping;
use App\Domain\Muscle\Filters\MuscleAreaFilterRules;
use App\Domain\Muscle\MuscleArea\GetMuscleAreaByIdUseCase;
use App\Domain\Muscle\MuscleArea\GetMuscleAreaContentsByIdUseCase;
use App\Domain\Muscle\MuscleArea\GetMuscleGroupsByMuscleAreaIdUseCase;
use App\Domain\Muscle\MuscleArea\ListMuscleAreasUseCase;
use App\Domain\Muscle\MuscleArea\UpsertContentMuscleAreaBulkUseCase;
use App\Domain\Muscle\MuscleArea\UpsertContentMuscleAreaUseCase;
use App\Domain\Muscle\Service\MuscleAreaService;
use App\Domain\Muscle\Service\MuscleGroupService;
use App\Domain\Muscle\Sort\MuscleAreaSortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Muscle\MuscleArea\GetMuscleAreaByIdHttp;
use App\UI\Adapters\Http\Muscle\MuscleArea\GetMuscleGroupsByMuscleAreaIdHttp;
use App\UI\Adapters\Http\Muscle\MuscleArea\ListMuscleAreasHttp;
use App\UI\Adapters\Http\Muscle\MuscleArea\UpsertContentMuscleAreaBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleArea\UpsertContentMuscleAreaHttp;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
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
#[Route(path: '/muscle-areas', name: 'muscle_areas_')]
final readonly class MuscleAreaController
{
    public function __construct(
        private MuscleAreaService  $muscleAreaService,
        private MuscleGroupService $muscleGroupService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_AREA_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of Muscle areas available in the system.',
        summary    : 'List of Muscle areas.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by Muscle area slug (exact match)',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // title
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[title][eq]',
                description: 'Filter by Muscle area title (exact match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[title][like]',
                description: 'Filter by Muscle area title (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // summary
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[summary][like]',
                description: 'Filter by Muscle area summary (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // details
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[details][like]',
                description: 'Filter by Muscle area details (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of Muscle areas',
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
                            type  : MuscleArea::class,
                            groups: [FrontGroupsEnum::MUSCLE_AREA_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                $request,
        ListMuscleAreasUseCase $useCase,
        NormalizerInterface    $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : MuscleAreaFilterMapping::FIELD_MAP,
            allowedOperators: MuscleAreaFilterRules::ALLOWED_OPERATORS,
            allowedFields   : MuscleAreaFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : MuscleAreaSortMapping::FIELD_MAP,
            defaultSort: 'slug'
        );

        try {
            $paginator = $useCase->execute(
                new ListMuscleAreasHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->muscleAreaService->transformCollectionToDTO(
            muscleAreas: $paginator->getItems(),
            filters    : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_AREA_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(
                paginator      : $paginator,
                paginatorValues: $paginatorValues
            )
        );
    }

    #[Route(
        path        : '/{muscleAreaId}',
        name        : 'detail',
        requirements: [
            'muscleAreaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_AREA_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific Muscle area.',
        summary    : 'Get Muscle area details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Muscle area details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Muscle area not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetMuscleAreaByIdUseCase $useCase,
        NormalizerInterface      $normalizer,
        string                   $muscleAreaId
    ): JsonResponse {
        try {
            $muscleArea = $useCase->execute(
                new GetMuscleAreaByIdHttp(
                    id: Uuid::fromString($muscleAreaId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->muscleAreaService->transformToDTO($muscleArea);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_AREA_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{muscleAreaId}/groups',
        name        : 'groups',
        requirements: [
            'muscleAreaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_GROUP_LIST)]
    #[OAT\Get(
        description: 'Returns groups for a specific Muscle area.',
        summary    : 'Get Muscle area groups.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Muscle area groups.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Muscle area not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function groups(
        GetMuscleGroupsByMuscleAreaIdUseCase $useCase,
        NormalizerInterface                  $normalizer,
        string                               $muscleAreaId
    ): JsonResponse {
        try {
            $result = $useCase->execute(
                new GetMuscleGroupsByMuscleAreaIdHttp(
                    muscleAreaId: Uuid::fromString($muscleAreaId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $dtoItems = $this->muscleGroupService->transformCollectionToDTO($result);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_AREA_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{muscleAreaId}/contents',
        name        : 'contents_list',
        requirements: [
            'muscleAreaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_AREA_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for an Muscle area.',
        summary    : 'List of all Muscle area contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all Muscle area contents.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : 'object',
                            groups: [FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function listContents(
        string                           $muscleAreaId,
        NormalizerInterface              $normalizer,
        GetMuscleAreaContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $muscleAreaContents = $useCase->execute(
                new GetMuscleAreaByIdHttp(
                    id: Uuid::fromString($muscleAreaId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($muscleAreaContents as $content) {
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
                context: [
                    'groups' => [
                        FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{muscleAreaId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'muscleAreaId' => '[0-9a-fA-F\-]+',
            'locale'       => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_AREA_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update Muscle area content for a given locale.',
        summary    : 'Upsert Muscle area localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['title', 'summary'],
                properties: [
                    new OAT\Property(
                        property: 'title',
                        type    : 'string',
                        example : 'Lorem ipsum'
                    ),
                    new OAT\Property(
                        property: 'summary',
                        type    : 'string',
                        example : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'
                    ),
                    new OAT\Property(
                        property: 'details',
                        type    : 'string',
                        nullable: true
                    ),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Muscle area not found.'),
        ]
    )]
    public function upsertContent(
        string                         $muscleAreaId,
        string                         $locale,
        Request                        $request,
        UpsertContentMuscleAreaUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleAreaHttp(
                    id     : Uuid::fromString($muscleAreaId),
                    locale : $locale,
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{muscleAreaId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'muscleAreaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_AREA_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an Muscle area.',
        summary    : 'Upsert multiple Muscle area contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "title"   => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "details" => null,
                    ],
                    "en" => [
                        "title"   => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => null,
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Muscle area not found.'),
        ]
    )]
    public function upsertContentsBulk(
        string                             $muscleAreaId,
        Request                            $request,
        UpsertContentMuscleAreaBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleAreaBulkHttp(
                    id     : Uuid::fromString($muscleAreaId),
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }
}
