<?php

namespace App\UI\Controller\Wod;

use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Filters\WodFilterMapping;
use App\Domain\Wod\Filters\WodFilterRules;
use App\Domain\Wod\Filters\WodScoreFilterMapping;
use App\Domain\Wod\Filters\WodScoreFilterRules;
use App\Domain\Wod\Service\WodScoreService;
use App\Domain\Wod\Service\WodService;
use App\Domain\Wod\Sort\WodSortMapping;
use App\Domain\Wod\Wod\CreateWodUseCase;
use App\Domain\Wod\Wod\GetWodByIdUseCase;
use App\Domain\Wod\Wod\GetWodLeaderboardUseCase;
use App\Domain\Wod\Wod\ListWodUseCase;
use App\Domain\Wod\Wod\UpdateWodUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Wod\Wod\CreateWodHttp;
use App\UI\Adapters\Http\Wod\Wod\GetWodByIdHttp;
use App\UI\Adapters\Http\Wod\Wod\ListWodsHttp;
use App\UI\Adapters\Http\Wod\Wod\UpdateWodHttp;
use App\UI\Adapters\Http\Wod\WodScore\GetWodLeaderboardHttp;
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
#[Route(path: '/wods', name: 'wod_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final class WodController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly WodService      $wodService,
        private readonly WodScoreService $wodScoreService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return a list of WODs available in the system.',
        summary    : 'List of WODs.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // name
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[name][eq]',
                description: 'Filter by WOD name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[name][like]',
                description: 'Filter by WOD name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // teamSize
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[teamSize][eq]',
                description: 'Filter by team size (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[teamSize][lt]',
                description: 'Filter by team size (less than)',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[teamSize][lte]',
                description: 'Filter by team size (less than or equal)',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[teamSize][gt]',
                description: 'Filter by team size (greater than)',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[teamSize][gte]',
                description: 'Filter by team size (greater than or equal)',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // type.id (wodType.id)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[type.id][eq]',
                description: 'Filter by WOD type ID',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // type.slug (wodType.slug)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[type.slug][eq]',
                description: 'Filter by WOD type slug',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // category.id (wodCategory.id)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[category.id][eq]',
                description: 'Filter by WOD category ID',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // category.slug (wodCategory.slug)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[category.slug][eq]',
                description: 'Filter by WOD category slug',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // division.id (wodVersions.wodDivision.id)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[division.id][eq]',
                description: 'Filter by WOD division ID.',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // division.slug (wodVersions.wodDivision.slug)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[division.slug][eq]',
                description: 'Filter by WOD division slug.',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // gender (wodVersions.wodVariants.gender)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[gender][eq]',
                description: 'Filter by gender.',
                required   : false,
                schema     : new OAT\Schema(type: 'string', enum: ['male', 'female', 'mixed'])
            ),

            // ---------------------------------------------------------------------------------------------------------
            // rounds (wodVersions.wodVariants.rounds)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[rounds][eq]',
                description: 'Filter by rounds (exact match, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[rounds][lt]',
                description: 'Filter by rounds (less than, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[rounds][lte]',
                description: 'Filter by rounds (less than or equal, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[rounds][gt]',
                description: 'Filter by rounds (greater than, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[rounds][gte]',
                description: 'Filter by rounds (greater than or equal, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // timeCap (wodVersions.wodVariants.timeCap)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[timeCap][eq]',
                description: 'Filter by time cap (exact match, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[timeCap][lt]',
                description: 'Filter by time cap (less than, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[timeCap][lte]',
                description: 'Filter by time cap (less than or equal, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[timeCap][gt]',
                description: 'Filter by time cap (greater than, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[timeCap][gte]',
                description: 'Filter by time cap (greater than or equal, in seconds).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of WODs',
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
                            type  : Wod::class,
                            groups: [FrontGroupsEnum::WOD_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request             $request,
        ListWodUseCase      $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : WodFilterMapping::FIELD_MAP,
            allowedOperators: WodFilterRules::ALLOWED_OPERATORS,
            allowedFields   : WodFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : WodSortMapping::FIELD_MAP,
            defaultSort: 'name'
        );

        try {
            $paginator = $useCase->execute(
                new ListWodsHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->wodService->transformCollectionToDTO(
            wods   : $paginator->getItems(),
            filters: $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_LIST,
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
    #[IsGranted(ListPermissions::PERMISSION_WOD_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Create a new WOD and returns the created resource ID.',
        summary    : 'Create a WOD.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a WOD.',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Wod::class,
                    groups: [FrontGroupsEnum::WOD_MANAGE]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'WOD created successfully.',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of WOD created.',
                        schema     : new OAT\Schema(type: 'string')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : Wod::class,
                        groups: [FrontGroupsEnum::WOD_MANAGE]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request             $request,
        CreateWodUseCase    $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);
            $wod     = $useCase->execute(
                new CreateWodHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $wod,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::WOD_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $wod->getId()->toRfc4122()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{wodId}',
        name        : 'detail',
        requirements: [
            'wodId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return detailed information for a specific WOD.',
        summary    : 'Get WOD details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of WOD.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Wod::class,
                        groups: [FrontGroupsEnum::WOD_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetWodByIdUseCase   $useCase,
        NormalizerInterface $normalizer,
        string              $wodId
    ): JsonResponse {
        try {
            $wod = $useCase->execute(
                new GetWodByIdHttp(
                    id: Uuid::fromString($wodId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->wodService->transformToDTO($wod);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{wodId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Update an existing WOD with the provided data.',
        summary    : 'Update a WOD.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update a WOD.',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Wod::class,
                    groups: [FrontGroupsEnum::WOD_MANAGE]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'wodId',
                description: 'UUID of the WOD to update.',
                required   : true,
                schema     : new OAT\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'WOD updated successfully.',
            ),
        ]
    )]
    public function patch(
        Request          $request,
        string           $wodId,
        UpdateWodUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateWodHttp(
                    id     : Uuid::fromString($wodId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{wodId}/division/{wodDivisionId}/leaderboard/{gender}',
        name        : 'leaderboard',
        requirements: [
            'wodId'         => '[0-9a-fA-F\-]+',
            'wodDivisionId' => '[0-9a-fA-F\-]+',
            'gender'        => 'male|female|mixed',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_SCORE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return the leaderboard for a specific WOD.',
        summary    : 'Get WOD leaderboard.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\PathParameter(
                name       : 'wodId',
                description: 'UUID of the WOD.',
                required   : true,
                schema     : new OAT\Schema(type: 'string', format: 'uuid')
            ),
            new OAT\PathParameter(
                name       : 'wodDivisionId',
                description: 'UUID of the WOD division.',
                required   : true,
                schema     : new OAT\Schema(type: 'string', format: 'uuid')
            ),
            new OAT\PathParameter(
                name       : 'gender',
                description: 'Gender filter for the leaderboard.',
                required   : true,
                schema     : new OAT\Schema(type: 'string', enum: ['male', 'female', 'mixed'])
            ),
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Leaderboard for the WOD.',
            ),
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'WOD not found.'),
        ]
    )]
    public function leaderboard(
        string                   $wodId,
        string                   $wodDivisionId,
        string                   $gender,
        Request                  $request,
        GetWodLeaderboardUseCase $useCase,
        NormalizerInterface      $normalizer,
    ): JsonResponse {
        $user = $this->getUser();
        $currentUser = $user instanceof User ? $user : null;

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : WodScoreFilterMapping::FIELD_MAP,
            allowedOperators: WodScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : WodScoreFilterRules::PUBLIC_FIELDS,
        );

        try {
            $paginator = $useCase->execute(
                new GetWodLeaderboardHttp(
                    wodId        : Uuid::fromString($wodId),
                    wodDivisionId: Uuid::fromString($wodDivisionId),
                    gender       : $gender,
                    page         : $paginatorValues->getPage(),
                    limit        : $paginatorValues->getLimit(),
                    filters      : $filters,
                ),
                currentUser: $currentUser,
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->wodScoreService->transformCollectionToDTO(
            wodScores: $paginator->getItems(),
            filters  : $filters,
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_LEADERBOARD,
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
}
