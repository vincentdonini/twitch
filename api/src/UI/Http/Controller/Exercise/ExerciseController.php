<?php

namespace App\UI\Http\Controller\Exercise;

use App\Domain\Exercise\Repository\ExerciseRepositoryInterface;
use App\Domain\Exercise\Service\ExerciseService;
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
#[Route('/exercises', name: 'exercise_')]
final class ExerciseController extends BaseController
{
    public function __construct(
        private readonly ExerciseRepositoryInterface $exerciseRepository,
        private readonly ExerciseService             $exerciseService,
        protected TranslatorInterface                $translator,
        protected SerializerInterface                $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all exercises available in the system.',
        summary    : 'Retrieve all exercises',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of exercises'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $exercises = $this->exerciseRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($exercise) => $this->exerciseService->transformToDTO($exercise), $exercises),
            serializationGroups: ['exercise:list'],
        );
    }

    #[Route('/{exerciseId}', name: 'detail', requirements: ['exerciseId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific exercise.',
        summary    : 'Get exercise details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Exercise details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Exercise not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $exerciseId): JsonResponse
    {
        $exercise = $this->exerciseRepository->findOneById($exerciseId);
        if (!$exercise) {
            return new JsonResponse(
                [
                    'error' => 'Exercise not found',
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->exerciseService->transformToDTO($exercise),
            serializationGroups: ['exercise:detail'],
        );
    }
}
