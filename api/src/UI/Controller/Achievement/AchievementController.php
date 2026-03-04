<?php

namespace App\UI\Controller\Achievement;

use App\Domain\Achievement\Achievement\GetAchievementContentsByIdUseCase;
use App\Domain\Achievement\Achievement\UpsertContentAchievementBulkUseCase;
use App\Domain\Achievement\Achievement\UpsertContentAchievementUseCase;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Filters\AchievementFilterMapping;
use App\Domain\Achievement\Filters\AchievementFilterRules;
use App\Domain\Achievement\Service\AchievementService;
use App\Domain\Achievement\Sort\AchievementSortMapping;
use App\Domain\Achievement\Achievement\CreateAchievementUseCase;
use App\Domain\Achievement\Achievement\GetAchievementByIdUseCase;
use App\Domain\Achievement\Achievement\ListAchievementUseCase;
use App\Domain\Achievement\Achievement\UpdateAchievementUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Achievement\Achievement\CreateAchievementHttp;
use App\UI\Adapters\Http\Achievement\Achievement\GetAchievementByIdHttp;
use App\UI\Adapters\Http\Achievement\Achievement\ListAchievementsHttp;
use App\UI\Adapters\Http\Achievement\Achievement\UpdateAchievementHttp;
use App\UI\Adapters\Http\Achievement\Achievement\UpsertContentAchievementBulkHttp;
use App\UI\Adapters\Http\Achievement\Achievement\UpsertContentAchievementHttp;
use InvalidArgumentException;
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
#[Route(path: '/achievements', name: 'achievement_')]
final class AchievementController extends AbstractController
{
    public function __construct(
        private readonly AchievementService $achievementService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of achievements available in the system.',
        summary    : 'List of achievements.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // code
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[code][eq]',
                description: 'Filter by Achievement code (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // achievementGroup
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[achievementGroup.id][eq]',
                description: 'Filter by achievementGroup ID.',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of achievements.',
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
                            type  : Achievement::class,
                            groups: [FrontGroupsEnum::ACHIEVEMENT_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                $request,
        ListAchievementUseCase $useCase,
        NormalizerInterface    $normalizer,
    ): JsonResponse {
        // Force the return of all results
        $request->query->set('page', 1);
        $request->query->set('limit', -1);

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : AchievementFilterMapping::FIELD_MAP,
            allowedOperators: AchievementFilterRules::ALLOWED_OPERATORS,
            allowedFields   : AchievementFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : AchievementSortMapping::FIELD_MAP,
            defaultSort: 'achievementCategory.position,achievementGroup.position,position'
        );

        try {
            $paginator = $useCase->execute(
                new ListAchievementsHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->achievementService->transformCollectionToDTO(
            achievements: $paginator->getItems(),
            filters     : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_LIST,
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
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Create a new Achievement and returns the created resource ID.',
        summary    : 'Create an achievement.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a achievement.',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Achievement::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_LIST]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Achievement created successfully.',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of Achievement created.',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : Achievement::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_LIST]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request                  $request,
        CreateAchievementUseCase $useCase,
        NormalizerInterface      $normalizer,
    ): JsonResponse {
        try {
            $payload     = json_decode($request->getContent(), true);
            $achievement = $useCase->execute(
                new CreateAchievementHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $this->achievementService->transformToDTO($achievement),
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::ACHIEVEMENT_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $achievement->getId()->toRfc4122()]
            );
        } catch (InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        } catch (AlreadyExistException) {
            $statusCode = Response::HTTP_CONFLICT;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{achievementId}',
        name        : 'detail',
        requirements: [
            'achievementId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific achievement.',
        summary    : 'Get Achievement details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of achievement.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Achievement::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetAchievementByIdUseCase $useCase,
        NormalizerInterface       $normalizer,
        string                    $achievementId
    ): JsonResponse {
        try {
            $achievement = $useCase->execute(
                new GetAchievementByIdHttp(
                    id: Uuid::fromString($achievementId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->achievementService->transformToDTO($achievement);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{achievementId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Update an existing Achievement with the provided data.',
        summary    : 'Update an achievement.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update an achievement.',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Achievement::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_LIST]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'code',
                description: 'Code of the achievement.',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\PathParameter(
                name       : 'position',
                description: 'Position of the achievement.',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
            new OAT\PathParameter(
                name       : 'achievementGroupId',
                description: 'Achievement group ID of the achievement.',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'Achievement updated successfully.',
            ),
        ]
    )]
    public function patch(
        Request                  $request,
        string                   $achievementId,
        UpdateAchievementUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateAchievementHttp(
                    id     : Uuid::fromString($achievementId),
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{achievementId}/contents',
        name        : 'contents_list',
        requirements: [
            'achievementId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an achievement.',
        summary    : 'Get Achievement contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Achievement not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                            $achievementId,
        NormalizerInterface               $normalizer,
        GetAchievementContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $achievementContents = $useCase->execute(
                new GetAchievementByIdHttp(
                    id: Uuid::fromString($achievementId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
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
                        FrontGroupsEnum::CONTENT_ACHIEVEMENT_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{achievementId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'achievementId' => '[0-9a-fA-F\-]+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_MANAGE)]
    #[OAT\Put(
        description: 'Create or update Achievement content for a given locale.',
        summary    : 'Upsert exercise localized content.',
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
            new OAT\Response(response: 404, description: 'Achievement not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                          $achievementId,
        string                          $locale,
        Request                         $request,
        UpsertContentAchievementUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementHttp(
                    id     : Uuid::fromString($achievementId),
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
        path        : '/{achievementId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: ['wodId' => '\d+'],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an achievement.',
        summary    : 'Upsert multiple Achievement contents.',
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
            new OAT\Response(response: 404, description: 'Achievement not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                              $achievementId,
        Request                             $request,
        UpsertContentAchievementBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementBulkHttp(
                    id     : Uuid::fromString($achievementId),
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
