<?php

namespace App\UI\Controller;

use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[Route(path: '/auth', name: 'auth_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
class SecurityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route(
        path   : '/register',
        name   : 'create',
        methods: ['POST']
    )]
    #[Security(name: null)]
    #[OAT\Post(
        description: 'Register a new user account.',
        summary    : 'User registration.',
        security   : [],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['email', 'password', 'firstName', 'lastName'],
                properties: [
                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OAT\Property(property: 'password', type: 'string', example: 'password123'),
                    new OAT\Property(property: 'firstName', type: 'string', example: 'John'),
                    new OAT\Property(property: 'lastName', type: 'string', example: 'Doe'),
                ]
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'User created successfully.',
                content    : new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'message', type: 'string', example: 'User created'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function register(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['firstName'], $data['lastName'])) {
            return new JsonResponse([
                'error' => 'Email, password, firstName and lastName are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = new User($data['email'], $data['firstName'], $data['lastName']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse([
                'error' => 'Invalid data',
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User created',
        ], Response::HTTP_CREATED);
    }
}
