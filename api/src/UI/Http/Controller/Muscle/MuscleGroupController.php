<?php

namespace App\UI\Http\Controller\Muscle;

use App\Domain\Muscle\Repository\MuscleGroupRepositoryInterface;
use App\Domain\Muscle\Repository\MuscleRepositoryInterface;
use App\Domain\Muscle\Service\MuscleGroupService;
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
#[Route('/muscle-groups', name: 'muscle_group_')]
final class MuscleGroupController extends BaseController
{
    public function __construct(
        private readonly MuscleGroupRepositoryInterface $muscleGroupRepository,
        private readonly MuscleRepositoryInterface      $muscleRepository,
        private readonly MuscleGroupService             $muscleGroupService,
        private readonly MuscleService                  $muscleService,
        protected TranslatorInterface                   $translator,
        protected SerializerInterface                   $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all muscle groups.',
        summary    : 'Retrieve all muscle groups',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of muscle groups'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $muscleGroups = $this->muscleGroupRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($muscleGroup) => $this->muscleGroupService->transformToDTO($muscleGroup), $muscleGroups),
            serializationGroups: ['muscleGroup:list']
        );
    }

    #[Route('/{muscleGroupId}', name: 'detail', requirements: ['muscleGroupId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific muscle group.',
        summary    : 'Get muscle group details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Muscle group details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle group not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $muscleGroupId): JsonResponse
    {
        $muscleGroup = $this->muscleGroupRepository->findOneById($muscleGroupId);

        if (!$muscleGroup) {
            return $this->errorJsonResponse(
                'muscle.group.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->muscleGroupService->transformToDTO($muscleGroup),
            serializationGroups: ['muscleGroup:detail']
        );
    }

    #[Route('/{muscleGroupId}/muscles', name: 'list_muscles', requirements: ['muscleGroupId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific muscle group.',
        summary    : 'Get muscle group details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Muscle group details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle group not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function listMuscles(string $muscleGroupId): JsonResponse
    {
        $muscleGroup = $this->muscleGroupRepository->findOneById($muscleGroupId);
        if (!$muscleGroup) {
            return $this->errorJsonResponse(
                'muscle.group.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscles = $this->muscleRepository->findAllByGroupId($muscleGroupId);

        return $this->listResourceJsonResponse(
            items              : array_map(fn($muscle) => $this->muscleService->transformToDTO($muscle), $muscles),
            serializationGroups: ['muscleGroup:detail']
        );
    }
}
