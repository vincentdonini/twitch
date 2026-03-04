<?php

namespace App\UI\Controller\Achievement;

use App\Domain\Achievement\AchievementGroup\GetAchievementGroupContentsByIdUseCase;
use App\Domain\Achievement\AchievementGroup\UpsertContentAchievementGroupBulkUseCase;
use App\Domain\Achievement\AchievementGroup\UpsertContentAchievementGroupUseCase;
use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Filters\AchievementGroupFilterMapping;
use App\Domain\Achievement\Filters\AchievementGroupFilterRules;
use App\Domain\Achievement\Service\AchievementGroupService;
use App\Domain\Achievement\Sort\AchievementGroupSortMapping;
use App\Domain\Achievement\AchievementGroup\CreateAchievementGroupUseCase;
use App\Domain\Achievement\AchievementGroup\GetAchievementGroupByIdUseCase;
use App\Domain\Achievement\AchievementGroup\ListAchievementGroupUseCase;
use App\Domain\Achievement\AchievementGroup\UpdateAchievementGroupUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Achievement\AchievementGroup\CreateAchievementGroupHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\GetAchievementGroupByIdHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\ListAchievementGroupsHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpdateAchievementGroupHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpsertContentAchievementGroupBulkHttp;
use App\UI\Adapters\Http\Achievement\AchievementGroup\UpsertContentAchievementGroupHttp;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(path: '/achievement-groups', name: 'achievement_group_')]
final class AchievementGroupController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly AchievementGroupService $achievementGroupService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_GROUP_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of achievement groups available in the system.',
        summary    : 'List of achievement groups.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // code
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[code][eq]',
                description: 'Filter by Achievement group code (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // achievementCategory
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[achievementCategory.id][eq]',
                description: 'Filter by achievement category ID.',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of achievement groups',
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
                            type  : AchievementGroup::class,
                            groups: [FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                     $request,
        ListAchievementGroupUseCase $useCase,
        NormalizerInterface         $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : AchievementGroupFilterMapping::FIELD_MAP,
            allowedOperators: AchievementGroupFilterRules::ALLOWED_OPERATORS,
            allowedFields   : AchievementGroupFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : AchievementGroupSortMapping::FIELD_MAP,
            defaultSort: 'position'
        );

        try {
            $paginator = $useCase->execute(
                new ListAchievementGroupsHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->achievementGroupService->transformCollectionToDTO(
            achievementGroups: $paginator->getItems(),
            filters          : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST,
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
        path   : '',
        name   : 'create',
        methods: ['POST']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_GROUP_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Create a new Achievement group and returns the created resource ID.',
        summary    : 'Create an achievement group.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created an achievement group',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : AchievementGroup::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Achievement group created successfully.',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of Achievement group created.',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : AchievementGroup::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request                       $request,
        CreateAchievementGroupUseCase $useCase,
        NormalizerInterface           $normalizer,
    ): JsonResponse {
        try {
            $payload          = json_decode($request->getContent(), true);
            $achievementGroup = $useCase->execute(
                new CreateAchievementGroupHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $this->achievementGroupService->transformToDTO($achievementGroup),
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::ACHIEVEMENT_GROUP_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $achievementGroup->getId()->toRfc4122()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{achievementGroupId}',
        name        : 'detail',
        requirements: [
            'achievementGroupId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_GROUP_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific achievement group.',
        summary    : 'Get Achievement group details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of achievement group.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : AchievementGroup::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetAchievementGroupByIdUseCase $useCase,
        NormalizerInterface            $normalizer,
        string                         $achievementGroupId
    ): JsonResponse {
        try {
            $achievementGroup = $useCase->execute(
                new GetAchievementGroupByIdHttp(
                    id: Uuid::fromString($achievementGroupId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->achievementGroupService->transformToDTO($achievementGroup);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{achievementGroupId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_GROUP_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Update an existing Achievement group with the provided data.',
        summary    : 'Update an achievement.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update an achievement group',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : AchievementGroup::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'code',
                description: 'Code of the achievement group.',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\PathParameter(
                name       : 'position',
                description: 'Position of the achievement group.',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
            new OAT\PathParameter(
                name       : 'achievementCategoryId',
                description: 'Achievement category ID of the achievement.',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'AchievementGroup updated successfully.',
            ),
        ]
    )]
    public function patch(
        Request                       $request,
        string                        $achievementGroupId,
        UpdateAchievementGroupUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateAchievementGroupHttp(
                    id     : Uuid::fromString($achievementGroupId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{achievementGroupId}/contents',
        name        : 'contents_list',
        requirements: [
            'achievementGroupId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_GROUP_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an achievement group.',
        summary    : 'Get Achievement group contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Achievement group not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                                 $achievementGroupId,
        NormalizerInterface                    $normalizer,
        GetAchievementGroupContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $achievementContents = $useCase->execute(
                new GetAchievementGroupByIdHttp(
                    id: Uuid::fromString($achievementGroupId)
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $contentsArray = [];
        foreach ($achievementContents as $content) {
            $contentsArray[$content->getLocale()] = [
                'title'       => $content->getTitle(),
                'description' => $content->getDescription(),
            ];
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $contentsArray,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{achievementGroupId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'achievementGroupId' => '[0-9a-fA-F\-]+',
            'locale'             => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_GROUP_MANAGE)]
    #[OAT\Put(
        description: 'Create or update Achievement group content for a given locale.',
        summary    : 'Upsert achievementGroup localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['title', 'description'],
                properties: [
                    new OAT\Property(
                        property: 'title',
                        type    : 'string',
                        example : 'Lorem ipsum'
                    ),
                    new OAT\Property(
                        property: 'description',
                        type    : 'string',
                        example : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'
                    ),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Content updated.'),
            new OAT\Response(response: 201, description: 'Content created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Achievement group not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                               $achievementGroupId,
        string                               $locale,
        Request                              $request,
        UpsertContentAchievementGroupUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementGroupHttp(
                    id     : Uuid::fromString($achievementGroupId),
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
        path        : '/{achievementGroupId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'achievementGroupId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_GROUP_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an achievement group.',
        summary    : 'Upsert multiple Achievement group contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "title"       => "Lorem ipsum",
                        "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    ],
                    "en" => [
                        "title"       => "Lorem ipsum",
                        "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Achievement group not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                                   $achievementGroupId,
        Request                                  $request,
        UpsertContentAchievementGroupBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementGroupBulkHttp(
                    id     : Uuid::fromString($achievementGroupId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
