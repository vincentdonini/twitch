<?php

namespace App\UI\Controller\Security;

use App\Domain\User\User\RegisterUserUseCase;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Security\RegisterUserHttp;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/auth', name: 'auth_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
class SecurityController extends AbstractController
{
    use ApiExceptionHandler;

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
    public function register(
        Request             $request,
        RegisterUserUseCase $useCase,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true) ?? [];

        try {
            $useCase->execute(new RegisterUserHttp($payload));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return new JsonResponse(
            data  : ['message' => 'User created'],
            status: Response::HTTP_CREATED,
        );
    }
}
