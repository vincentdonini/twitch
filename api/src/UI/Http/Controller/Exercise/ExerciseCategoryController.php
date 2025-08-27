<?php

namespace App\UI\Http\Controller\Exercise;

use App\Domain\Exercise\Service\ExerciseCategoryService;
use App\Infrastructure\Persistence\Doctrine\Exercise\ExerciseCategoryRepository;
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
#[Route('/exercise-categories', name: 'exercise_category_')]
final class ExerciseCategoryController extends BaseController
{
    public function __construct(
        private readonly ExerciseCategoryRepository $exerciseCategoryRepository,
        private readonly ExerciseCategoryService    $exerciseCategoryService,
        protected TranslatorInterface               $translator,
        protected SerializerInterface               $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all exercise categories available in the system.',
        summary    : 'Retrieve all exercise categories',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of exercise categories'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $exercises = $this->exerciseCategoryRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($exercise) => $this->exerciseCategoryService->transformToDTO($exercise), $exercises),
            serializationGroups: ['exerciseCategory:list'],
        );
    }

    #[Route('/{exerciseCategoryId}', name: 'detail', requirements: ['exerciseCategoryId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific exercise category.',
        summary    : 'Get exercise category details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Exercise category details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Exercise category not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $exerciseCategoryId): JsonResponse
    {
        $exerciseCategory = $this->exerciseCategoryRepository->findOneById($exerciseCategoryId);
        if (!$exerciseCategory) {
            return new JsonResponse(['error' => 'Exercise category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->singleResourceJsonResponse(
            item               : $this->exerciseCategoryService->transformToDTO($exerciseCategory),
            serializationGroups: ['exerciseCategory:detail'],
        );
    }
}
