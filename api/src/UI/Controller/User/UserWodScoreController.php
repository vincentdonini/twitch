<?php

namespace App\UI\Controller\User;

use App\Domain\User\Ports\UserDALInterface;
use App\Domain\Wod\Filters\WodScoreFilterMapping;
use App\Domain\Wod\Filters\WodScoreFilterRules;
use App\Domain\Wod\Service\WodScoreService;
use App\Domain\Wod\Sort\WodScoreSortMapping;
use App\Domain\Wod\WodScore\ListWodScoresUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Wod\WodScore\ListWodScoresHttp;
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
#[Route(path: '/users', name: 'user_wod_scores_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final class UserWodScoreController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly WodScoreService  $wodScoreService,
        private readonly UserDALInterface $userDAL,
    ) {
    }

    #[Route(
        path        : '/{userId}/wod-scores',
        name        : 'list',
        requirements: [
            'userId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_SCORE_LIST)]
    #[OAT\Get(
        description: 'Returns a paginated list of WOD scores for a specific user.',
        summary    : 'Get WOD scores for a user',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of WOD scores.',
                headers    : [
                    new OAT\Header(ref: '#/components/headers/Element-Count', header: 'Element-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Page', header: 'Pagination-Page'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Count', header: 'Pagination-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Limit', header: 'Pagination-Limit'),
                ],
            ),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        Request               $request,
        ListWodScoresUseCase  $useCase,
        NormalizerInterface   $normalizer,
        string                $userId
    ): JsonResponse {
        $user = $this->userDAL->getById(Uuid::fromString($userId));
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : WodScoreFilterMapping::FIELD_MAP,
            allowedOperators: WodScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : WodScoreFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : WodScoreSortMapping::FIELD_MAP,
            defaultSort: '-performedAt'
        );

        $paginator = $useCase->execute(
            new ListWodScoresHttp(
                page   : $paginatorValues->getPage(),
                limit  : $paginatorValues->getLimit(),
                filters: $filters,
                sorts  : $sorts,
            ),
            currentUser: $user
        );

        $dtoItems = $this->wodScoreService->transformCollectionToDTO(
            wodScores: $paginator->getItems(),
            filters  : $filters
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
        path   : '/me/wod-scores',
        name   : 'me_wod_scores',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OAT\Get(
        description: 'Returns the WOD scores of the currently authenticated user.',
        summary    : 'Get WOD scores for current user',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of WOD scores.',
                headers    : [
                    new OAT\Header(ref: '#/components/headers/Element-Count', header: 'Element-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Page', header: 'Pagination-Page'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Count', header: 'Pagination-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Limit', header: 'Pagination-Limit'),
                ],
            ),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function me(
        Request              $request,
        ListWodScoresUseCase $useCase,
        NormalizerInterface  $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : WodScoreFilterMapping::FIELD_MAP,
            allowedOperators: WodScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : WodScoreFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : WodScoreSortMapping::FIELD_MAP,
            defaultSort: '-performedAt'
        );

        $paginator = $useCase->execute(
            new ListWodScoresHttp(
                page   : $paginatorValues->getPage(),
                limit  : $paginatorValues->getLimit(),
                filters: $filters,
                sorts  : $sorts,
            ),
            currentUser: $this->getUser()
        );

        $dtoItems = $this->wodScoreService->transformCollectionToDTO(
            wodScores: $paginator->getItems(),
            filters  : $filters
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
}
