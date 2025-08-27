<?php

namespace App\UI\Http\Controller\Wod;

use App\Domain\Wod\Repository\WodCategoryRepositoryInterface;
use App\Domain\Wod\Service\WodCategoryService;
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
#[Route('/wod-categories', name: 'wod_category_')]
final class WodCategoryController extends BaseController
{
    public function __construct(
        private readonly WodCategoryRepositoryInterface $wodCategoryRepository,
        private readonly WodCategoryService             $wodCategoryService,
        protected TranslatorInterface                   $translator,
        protected SerializerInterface                   $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get WOD category list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'WOD category list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $wodCategories = $this->wodCategoryRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($wodCategory) => $this->wodCategoryService->transformToDTO($wodCategory), $wodCategories),
            serializationGroups: ['wodVersion:list']
        );
    }

    #[Route('/{wodCategoryId}', name: 'detail', requirements: ['wodCategoryId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        summary  : 'Get WOD category details by ID',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Get WOD category details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'WOD category not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $wodCategoryId): JsonResponse
    {
        $wodCategory = $this->wodCategoryRepository->findOneById($wodCategoryId);
        if (!$wodCategory) {
            return $this->errorJsonResponse(
                'wod.category.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->wodCategoryService->transformToDTO($wodCategory),
            serializationGroups: ['wodVersion:detail']
        );
    }
}
