<?php

namespace App\UI\Controller\Muscle;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Filters\MuscleGroupFilterMapping;
use App\Domain\Muscle\Filters\MuscleGroupFilterRules;
use App\Domain\Muscle\MuscleGroup\GetMuscleGroupByIdUseCase;
use App\Domain\Muscle\MuscleGroup\GetMuscleGroupContentsByIdUseCase;
use App\Domain\Muscle\MuscleGroup\GetMusclesByMuscleGroupIdUseCase;
use App\Domain\Muscle\MuscleGroup\ListMuscleGroupsUseCase;
use App\Domain\Muscle\MuscleGroup\UpsertContentMuscleGroupBulkUseCase;
use App\Domain\Muscle\MuscleGroup\UpsertContentMuscleGroupUseCase;
use App\Domain\Muscle\Service\MuscleGroupService;
use App\Domain\Muscle\Service\MuscleService;
use App\Domain\Muscle\Sort\MuscleGroupSortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Muscle\MuscleGroup\GetMuscleGroupByIdHttp;
use App\UI\Adapters\Http\Muscle\MuscleGroup\GetMusclesByMuscleGroupIdHttp;
use App\UI\Adapters\Http\Muscle\MuscleGroup\ListMuscleGroupsHttp;
use App\UI\Adapters\Http\Muscle\MuscleGroup\UpsertContentMuscleGroupBulkHttp;
use App\UI\Adapters\Http\Muscle\MuscleGroup\UpsertContentMuscleGroupHttp;
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
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
#[Route(path: '/muscle-groups', name: 'muscle_groups_')]
final readonly class MuscleGroupController
{
    public function __construct(
        private MuscleGroupService $muscleGroupService,
        private MuscleService      $muscleService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_GROUP_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of muscle groups available in the system.',
        summary    : 'List of muscle groups',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
            new OAT\Parameter(
                name       : 'slug',
                description: 'Filter result by slug.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'title',
                description: 'Filter result by title.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'summary',
                description: 'Filter result by summary.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'details',
                description: 'Filter result by details.',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of muscle groups',
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
                            type  : MuscleGroup::class,
                            groups: [FrontGroupsEnum::MUSCLE_GROUP_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                 $request,
        ListMuscleGroupsUseCase $useCase,
        SerializerInterface     $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : MuscleGroupFilterMapping::FIELD_MAP,
            allowedOperators: MuscleGroupFilterRules::ALLOWED_OPERATORS,
            allowedFields   : MuscleGroupFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : MuscleGroupSortMapping::FIELD_MAP,
            defaultSort: 'slug'
        );

        try {
            $paginator = $useCase->execute(
                new ListMuscleGroupsHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->muscleGroupService->transformCollectionToDTO(
            muscleGroups: $paginator->getItems(),
            filters     : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_GROUP_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{muscleGroupId}',
        name        : 'detail',
        requirements: [
            'muscleGroupId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_GROUP_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific muscle group.',
        summary    : 'Get muscle group details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Muscle group details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Muscle group not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetMuscleGroupByIdUseCase $useCase,
        SerializerInterface       $normalizer,
        string                    $muscleGroupId
    ): JsonResponse {
        try {
            $muscleGroup = $useCase->execute(
                new GetMuscleGroupByIdHttp($muscleGroupId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->muscleGroupService->transformToDTO($muscleGroup);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_GROUP_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{muscleGroupId}/muscles',
        name        : 'groups',
        requirements: [
            'muscleGroupId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_GROUP_LIST)]
    #[OAT\Get(
        description: 'Returns muscles for a specific muscle group.',
        summary    : 'Get muscle group muscles',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Muscle group muscles'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Muscle group not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function groups(
        GetMusclesByMuscleGroupIdUseCase $useCase,
        NormalizerInterface              $normalizer,
        string                           $muscleGroupId
    ): JsonResponse {
        try {
            $result = $useCase->execute(
                new GetMusclesByMuscleGroupIdHttp($muscleGroupId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $dtoItems = $this->muscleService->transformCollectionToDTO($result);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_LIST,
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
        path        : '/{muscleGroupId}/contents',
        name        : 'contents_list',
        requirements: [
            'muscleGroupId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_GROUP_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for an muscle group',
        summary    : 'List of all muscle group contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all muscle group contents',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : 'object',
                            groups: [FrontGroupsEnum::CONTENT_MUSCLE_GROUP_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function listContents(
        string                            $muscleGroupId,
        NormalizerInterface               $normalizer,
        GetMuscleGroupContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $muscleGroupContents = $useCase->execute(
                new GetMuscleGroupByIdHttp($muscleGroupId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($muscleGroupContents as $content) {
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
                        FrontGroupsEnum::CONTENT_MUSCLE_GROUP_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{muscleGroupId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'muscleGroupId' => '\d+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_GROUP_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update muscle group content for a given locale.',
        summary    : 'Upsert muscle group localized content',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['name', 'summary'],
                properties: [
                    new OAT\Property(property: 'name', type: 'string', example: 'Lorem ipsum'),
                    new OAT\Property(property: 'summary', type: 'string', example: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'),
                    new OAT\Property(property: 'details', type: 'string', nullable: true),
                ]
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Contents upserted successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'Resource ID of the upserted muscle group contents'
                    ),
                ]
            ),
        ]
    )]
    public function upsertContent(
        string                          $muscleGroupId,
        string                          $locale,
        Request                         $request,
        UpsertContentMuscleGroupUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleGroupHttp(
                    id     : $muscleGroupId,
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
        path        : '/{muscleGroupId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'muscleGroupId' => '\d+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_GROUP_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an muscle group',
        summary    : 'Upsert multiple muscle group contents',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => "",
                    ],
                    "en" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => "",
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Contents upserted successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'Resource ID of the upserted contents'
                    ),
                ]
            ),
        ]
    )]
    public function upsertContentsBulk(
        string                              $muscleGroupId,
        Request                             $request,
        UpsertContentMuscleGroupBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleGroupBulkHttp(
                    id     : $muscleGroupId,
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
