<?php

namespace App\UI\Http\Controller\Movement;

use App\Domain\Movement\Repository\MovementRepositoryInterface;
use App\Domain\Movement\Service\MovementService;
use App\UI\Http\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
#[Route('/movements', name: 'movement_')]
final class MovementController extends BaseController
{
    public function __construct(
        private readonly MovementRepositoryInterface $movementRepository,
        private readonly MovementService             $movementService,
        protected TranslatorInterface                $translator,
        protected SerializerInterface                $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all movements available in the system.',
        summary    : 'Retrieve all movements',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of movements'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $movements = $this->movementRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($movement) => $this->movementService->transformToDTO($movement), $movements),
            serializationGroups: ['movement:list'],
        );
    }

    #[Route('/{movementId}', name: 'detail', requirements: ['movementId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific movement.',
        summary    : 'Get movement details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Movement details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Movement not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $movementId): JsonResponse
    {
        $movement = $this->movementRepository->findOneById($movementId);
        if (!$movement) {
            return $this->errorJsonResponse(
                'movement.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->movementService->transformToDTO($movement),
            serializationGroups: ['movement:detail'],
        );
    }
}
