<?php

namespace App\Controller;

use App\Repository\UserRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use Symfony\Bundle\SecurityBundle\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
#[Route('/user', name: 'user_')]
final class UserController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SerializerInterface     $serializer
    ) {
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
    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAllUsers();
        $json  = $this->serializer->serialize(
            $users,
            'json',
            ['groups' => 'user:list']
        );

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
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
    public function getUser(int $id, Security $security, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->userRepository->findUserById($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // HOOK: Self-user or ADMIN
        $currentUser = $security->getUser();
        if (!$currentUser || ($currentUser->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $currentUser->getRoles()))) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $json = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'user:detail']
        );

        return new JsonResponse($json, Response::HTTP_OK, [], true);
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
        $user = $userRepository->findUserById($id);
        if (!$user) {
            return new JsonResponse([
                'error' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // HOOK: Can delete your-self
        $currentUser = $security->getUser();
        if ($currentUser->getId() === $user->getId()) {
            return new JsonResponse([
                'error' => 'You cannot delete yourself',
            ], Response::HTTP_FORBIDDEN);
        }

        $userRepository->delete($user);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
