<?php

namespace App\UI\Http\Controller\User;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\UserService;
use App\UI\Http\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
#[Route('/users', name: 'user_')]
final class UserController extends BaseController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserService             $userService,
        protected TranslatorInterface            $translator,
        protected SerializerInterface            $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get User list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'User list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(Request $request): JsonResponse
    {
//        $params  = $this->getPaginationAndSortingParameters($request);
//        $search  = $request->query->get('search') ?: null;
//        $filters = [];
//
//        $users = $this->userRepository->findByFilters(
//            sortBy   : $params['sortBy'],
//            sortOrder: $params['sortOrder'],
//            offset   : $params['offset'],
//            limit    : $params['limit'],
//            search   : $search,
//            filters  : $filters
//        );
//
//        return $this->paginatedResourceJsonResponse(
//            total              : $this->userRepository->countByFilters($search, $filters),
//            offset             : $params['offset'],
//            limit              : $params['displayLimit'],
//            items              : array_map(
//                fn($user) => $this->userService->transformToDTO($user),
//                $users
//            ),
//            serializationGroups: ['wod:list']
//        );
    }

    #[Route('/{userId}', name: 'detail', requirements: ['userId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        summary  : 'Get user details by ID',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Get user details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function getAppUser(int $userId, Security $security): JsonResponse
    {
//        $user = $this->userRepository->findUserById($userId);
//        if (!$user) {
//            return $this->errorJsonResponse(
//                'user.not_found',
//                Response::HTTP_NOT_FOUND,
//            );
//        }
//
//        // HOOK: Self-user or ADMIN
//        $currentUser = $security->getUser();
//        if (!$currentUser || ($currentUser->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $currentUser->getRoles()))) {
//            return $this->errorJsonResponse(
//                'auth.access_denied',
//                Response::HTTP_FORBIDDEN,
//            );
//        }
//
//        return $this->singleResourceJsonResponse(
//            item               : $this->userService->transformToDTO($user),
//            serializationGroups: ['user:detail'],
//        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Delete(
        summary  : 'Delete user',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 204, description: 'Deleted user'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function delete(int $id, UserRepositoryInterface $userRepository, Security $security): JsonResponse
    {
//        $user = $userRepository->findUserById($id);
//        if (!$user) {
//            return $this->errorJsonResponse(
//                'user.not_found',
//                Response::HTTP_NOT_FOUND,
//            );
//        }
//
//        // HOOK: Can delete your-self
//        $currentUser = $security->getUser();
//        if ($currentUser->getId() === $user->getId()) {
//            return $this->errorJsonResponse(
//                'user.self_deletion_denied',
//                Response::HTTP_FORBIDDEN,
//            );
//        }
//
//        $userRepository->delete($user);
//
//        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
