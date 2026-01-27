<?php

namespace App\UI\Controller\Wod;

use App\Domain\Equipment\Sort\EquipmentSortMapping;
use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Filters\WodScoreFilterMapping;
use App\Domain\Wod\Filters\WodScoreFilterRules;
use App\Domain\Wod\Service\WodScoreService;
use App\Domain\Wod\Sort\WodScoreSortMapping;
use App\Domain\Wod\WodScore\CreateWodScoreUseCase;
use App\Domain\Wod\WodScore\GetWodScoreByIdUseCase;
use App\Domain\Wod\WodScore\ListWodScoresUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Wod\WodScore\CreateWodScoreHttp;
use App\UI\Adapters\Http\Wod\WodScore\GetWodScoreByIdHttp;
use App\UI\Adapters\Http\Wod\WodScore\ListWodScoresHttp;
use Doctrine\ORM\EntityNotFoundException;
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

#[AsController]
#[Route(path: '/wod-scores', name: 'wod_score_')]
final class WodScoreController extends AbstractController
{
    public function __construct(
        private readonly WodScoreService $wodScoreService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_SCORE_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all WOD scores available in the system.',
        summary    : 'Retrieve all WOD scores',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // user
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[user.id][eq]',
                description: 'Filter by user ID (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.firstName][eq]',
                description: 'Filter by user firstName (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.firstName][like]',
                description: 'Filter by user firstName (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.lastName][eq]',
                description: 'Filter by user lastName (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.lastName][like]',
                description: 'Filter by user firstName (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // wod
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[wod.id][eq]',
                description: 'Filter by WOD ID (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[wod.name][eq]',
                description: 'Filter by WOD name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[wod.name][like]',
                description: 'Filter by WOD name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD scores'),
            new OAT\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
        Request              $request,
        ListWodScoresUseCase $useCase,
        NormalizerInterface  $normalizer,
    ): JsonResponse {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : WodScoreFilterMapping::FIELD_MAP,
            allowedOperators: WodScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : WodScoreFilterRules::PUBLIC_FIELDS
        );

        try {
            $paginator = $useCase->execute(
                new ListWodScoresHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                ),
                $currentUser
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
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
                        FrontGroupsEnum::WOD_SCORE_LIST,
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
    #[IsGranted(ListPermissions::PERMISSION_WOD_SCORE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Creates a new WOD score and returns the created resource ID.',
        summary    : 'Create a WOD',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a WOD score',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : WodScore::class,
                    groups: [FrontGroupsEnum::WOD_SCORE_MANAGE]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'WOD score created successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of WOD score created',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : WodScore::class,
                        groups: [FrontGroupsEnum::WOD_SCORE_MANAGE]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request               $request,
        CreateWodScoreUseCase $useCase,
        NormalizerInterface   $normalizer,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new InvalidArgumentException();
        }

        try {
            $payload  = json_decode($request->getContent(), true);
            $wodScore = $useCase->execute(
                new CreateWodScoreHttp(
                    payload: $payload,
                    userId : $user->getId()
                )
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $wodScore,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::WOD_SCORE_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $wodScore->getId()]
            );
        } catch (InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{wodScoreId}',
        name        : 'detail',
        requirements: [
            'wodScoreId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_SCORE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD score.',
        summary    : 'Get WOD score details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD score details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD score not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetWodScoreByIdUseCase $useCase,
        NormalizerInterface    $normalizer,
        string                 $wodScoreId
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_FORBIDDEN
            );
        }

        try {
            $wodScore = $useCase->execute(
                new GetWodScoreByIdHttp(
                    id: $wodScoreId
                )
            );

            if (
                $wodScore->getUser()->getId() !== $user->getId() &&
                !in_array('ROLE_ADMIN', $user->getRoles(), true)
            ) {
                return new JsonResponse(
                    data  : null,
                    status: Response::HTTP_FORBIDDEN
                );
            }
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->wodScoreService->transformToDTO($wodScore);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_SCORE_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
