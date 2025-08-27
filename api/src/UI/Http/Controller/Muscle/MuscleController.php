<?php

namespace App\UI\Http\Controller\Muscle;

use App\Domain\Muscle\Repository\MuscleRepositoryInterface;
use App\Domain\Muscle\Service\MuscleService;
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
#[Route('/muscles', name: 'muscle_')]
final class MuscleController extends BaseController
{
    public function __construct(
        private readonly MuscleRepositoryInterface $muscleRepository,
        private readonly MuscleService             $muscleService,
        protected TranslatorInterface              $translator,
        protected SerializerInterface              $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all muscles.',
        summary    : 'Retrieve all muscles',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of muscles'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $muscles = $this->muscleRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($muscle) => $this->muscleService->transformToDTO($muscle), $muscles),
            serializationGroups: ['muscle:list']
        );
    }

    #[Route('/{muscleId}', name: 'detail', requirements: ['muscleId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific muscle.',
        summary    : 'Get muscle details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Muscle details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $muscleId): JsonResponse
    {
        $muscle = $this->muscleRepository->findOneById($muscleId);
        if (!$muscle) {
            return $this->errorJsonResponse(
                'muscle.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->muscleService->transformToDTO($muscle),
            serializationGroups: ['muscle:detail']
        );
    }
}
