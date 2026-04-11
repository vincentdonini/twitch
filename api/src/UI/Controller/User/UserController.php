<?php

namespace App\UI\Controller\User;

use App\Application\User\DTO\UserMeCompanyDTO;
use App\Application\User\DTO\UserMeGymSubscriptionDTO;
use App\Application\User\DTO\UserMePlaceDTO;
use App\Domain\Organization\Ports\CompanyDALInterface;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
use App\Domain\User\Service\UserService;
use App\Domain\User\User\GetUserByIdUseCase;
use App\Domain\User\User\ListUsersUseCase;
use App\Domain\User\User\UpdatePasswordUseCase;
use App\Domain\User\User\UpdateUserMeUseCase;
use App\UI\Adapters\Http\User\User\UpdatePasswordHttp;
use App\UI\Adapters\Http\User\User\UpdateUserMeHttp;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\User\User\GetUserByIdHttp;
use App\UI\Adapters\Http\User\User\ListUsersHttp;
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
#[Route(path: '/users', name: 'user_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final class UserController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_USER_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all users available in the system.',
        summary    : 'Retrieve all users',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of users.',
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
    public function list(
        Request             $request,
        ListUsersUseCase    $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $search = (string) $request->query->get('search', '');

        try {
            $paginator = $useCase->execute(
                new ListUsersHttp(
                    page  : $paginatorValues->getPage(),
                    limit : $paginatorValues->getLimit(),
                    search: $search,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->userService->transformCollectionToDTO($paginator->getItems());

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{userId}',
        name        : 'detail',
        requirements: [
            'userId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_USER_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific user.',
        summary    : 'Get user details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: Response::HTTP_OK, description: 'User details.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetUserByIdUseCase  $useCase,
        NormalizerInterface $normalizer,
        string              $userId
    ): JsonResponse {
        try {
            $user = $useCase->execute(
                new GetUserByIdHttp(
                    id: Uuid::fromString($userId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->userService->transformToDTO($user);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/me',
        name   : 'me',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OAT\Get(
        description: 'Returns information about the currently authenticated user.',
        summary    : 'Get current user profile',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: Response::HTTP_OK, description: 'Current user profile.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function me(
        GetUserByIdUseCase       $useCase,
        NormalizerInterface      $normalizer,
        SubscriptionDALInterface $subscriptionRepository,
        PlaceDALInterface        $placeRepository,
        CompanyDALInterface      $companyRepository,
    ): JsonResponse {
        $authenticatedUser = $this->getUser();

        if (!$authenticatedUser) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        try {
            $user = $useCase->execute(
                new GetUserByIdHttp($authenticatedUser->getId())
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->userService->transformToDTO($user);

        $dtoItem->gymSubscriptions = array_map(
            fn($s) => UserMeGymSubscriptionDTO::fromSubscription($s),
            $subscriptionRepository->findActiveByUser($user)
        );

        $dtoItem->coachPlaces = array_map(
            fn($p) => UserMePlaceDTO::fromPlace($p),
            $placeRepository->findByCoach($user)
        );

        $dtoItem->ownerCompanies = array_map(
            fn($c) => UserMeCompanyDTO::fromCompany($c),
            $companyRepository->findByOwner($user)
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_ME,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/me',
        name   : 'update_me',
        methods: ['PATCH']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Security(name: 'bearerAuth')]
    public function updateMe(
        Request              $request,
        UpdateUserMeUseCase  $useCase,
        GetUserByIdUseCase   $getUserUseCase,
        NormalizerInterface  $normalizer,
    ): JsonResponse {
        $authenticatedUser = $this->getUser();

        if (!$authenticatedUser) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        $body = json_decode($request->getContent(), true) ?? [];

        try {
            $user = $getUserUseCase->execute(
                new GetUserByIdHttp($authenticatedUser->getId())
            );

            $updated = $useCase->execute(
                $user,
                new UpdateUserMeHttp(
                    email    : $body['email'] ?? null,
                    firstName: $body['firstName'] ?? null,
                    lastName : $body['lastName'] ?? null,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $this->userService->transformToDTO($updated),
                format : 'json',
                context: ['groups' => [FrontGroupsEnum::USER_ME]],
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/me/password',
        name   : 'update_password',
        methods: ['PUT']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Security(name: 'bearerAuth')]
    public function updatePassword(
        Request               $request,
        UpdatePasswordUseCase $useCase,
        GetUserByIdUseCase    $getUserUseCase,
    ): JsonResponse {
        $authenticatedUser = $this->getUser();

        if (!$authenticatedUser) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        $body = json_decode($request->getContent(), true) ?? [];

        $currentPassword = $body['currentPassword'] ?? '';
        $newPassword     = $body['newPassword'] ?? '';

        if (!$currentPassword || !$newPassword) {
            return new JsonResponse(['message' => 'Missing required fields.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $getUserUseCase->execute(
                new GetUserByIdHttp($authenticatedUser->getId())
            );

            $useCase->execute(
                $user,
                new UpdatePasswordHttp(
                    currentPassword: $currentPassword,
                    newPassword    : $newPassword,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
