<?php

namespace App\UI\Http\Controller\Muscle;

use App\Domain\Muscle\Repository\MuscleAreaRepositoryInterface;
use App\Domain\Muscle\Repository\MuscleGroupRepositoryInterface;
use App\Domain\Muscle\Repository\MuscleRepositoryInterface;
use App\Domain\Muscle\Service\MuscleAreaService;
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
#[Route('/muscle-areas', name: 'muscle_area_')]
final class MuscleAreaController extends BaseController
{
    public function __construct(
        private readonly MuscleAreaRepositoryInterface  $muscleAreaRepository,
        private readonly MuscleGroupRepositoryInterface $muscleGroupRepository,
        private readonly MuscleRepositoryInterface      $muscleRepository,
        private readonly MuscleAreaService              $muscleAreaService,
        private readonly MuscleGroupService             $muscleGroupService,
        private readonly MuscleService                  $muscleService,
        protected TranslatorInterface                   $translator,
        protected SerializerInterface                   $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all muscle areas available in the system.',
        summary    : 'Retrieve all muscle areas',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of muscle areas'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $muscleAreas = $this->muscleAreaRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($muscleArea) => $this->muscleAreaService->transformToDTO($muscleArea), $muscleAreas),
            serializationGroups: ['muscleArea:list']
        );
    }

    #[Route('/{muscleAreaId}', name: 'detail', requirements: ['muscleAreaId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific muscle area.',
        summary    : 'Get muscle area details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Muscle area details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle area not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $muscleAreaId): JsonResponse
    {
        $muscleArea = $this->muscleAreaRepository->findOneById($muscleAreaId);
        if (!$muscleArea) {
            return $this->errorJsonResponse(
                'muscle.area.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->muscleAreaService->transformToDTO($muscleArea),
            serializationGroups: ['muscleArea:detail']
        );
    }

    #[Route('/{muscleAreaId}/groups', name: 'list_groups', requirements: ['muscleAreaId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns all muscle groups associated with a given muscle area.',
        summary    : 'Get muscle groups for a specific muscle area',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of muscle groups'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle area not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function listGroups(string $muscleAreaId): JsonResponse
    {
        $muscleArea = $this->muscleAreaRepository->findOneById($muscleAreaId);
        if (!$muscleArea) {
            return $this->errorJsonResponse(
                'muscle.area.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscleGroups = $this->muscleGroupRepository->findAllByAreaId($muscleAreaId);

        return $this->listResourceJsonResponse(
            items              : array_map(fn($item) => $this->muscleGroupService->transformToDTO($item), $muscleGroups),
            serializationGroups: ['muscleGroup:list']
        );
    }

    #[Route('/{muscleAreaId}/groups/{muscleGroupId}', name: 'group_detail', requirements: ['muscleAreaId' => '\d+', 'muscleGroupId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific muscle group linked to a specific muscle area.',
        summary    : 'Get a muscle group by ID for a given muscle area',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Muscle group details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Muscle area or group not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function getGroupDetail(string $muscleAreaId, string $muscleGroupId): JsonResponse
    {
        $muscleArea = $this->muscleAreaRepository->findOneById($muscleAreaId);
        if (!$muscleArea) {
            return $this->errorJsonResponse(
                'muscle.area.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscleGroup = $this->muscleGroupRepository->findOneById($muscleGroupId);
        if (!$muscleGroup) {
            return $this->errorJsonResponse(
                'muscle.group.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        if ($muscleGroup->getArea() !== $muscleArea) {
            return $this->errorJsonResponse(
                'muscle.group.invalid-area',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscleGroup = $this->muscleGroupRepository->findOneByAreaId($muscleAreaId, $muscleGroupId);

        return $this->singleResourceJsonResponse(
            item               : $this->muscleGroupService->transformToDTO($muscleGroup),
            serializationGroups: ['muscleGroup:list']
        );
    }

    #[Route('/{muscleAreaId}/groups/{muscleGroupId}/muscles', name: 'list_muscles', requirements: ['muscleAreaId' => '\d+', 'groupId' => '\d+'], methods: ['GET'])]
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
    public function listMuscles(string $muscleAreaId, string $muscleGroupId): JsonResponse
    {
        $muscleArea = $this->muscleAreaRepository->findOneById($muscleAreaId);
        if (!$muscleArea) {
            return $this->errorJsonResponse(
                'muscle.area.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscleGroup = $this->muscleGroupRepository->findOneById($muscleGroupId);
        if (!$muscleGroup) {
            return $this->errorJsonResponse(
                'muscle.group.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        if ($muscleGroup->getArea() !== $muscleArea) {
            return $this->errorJsonResponse(
                'muscle.group.invalid-area',
                Response::HTTP_NOT_FOUND,
            );
        }

        $muscles = $this->muscleRepository->findAllByAreaIdAndGroupId($muscleAreaId, $muscleGroupId);

        return $this->listResourceJsonResponse(
            items              : array_map(fn($muscle) => $this->muscleService->transformToDTO($muscle), $muscles),
            serializationGroups: ['muscleGroup:detail']
        );
    }
}
