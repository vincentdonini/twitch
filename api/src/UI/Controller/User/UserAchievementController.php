<?php

namespace App\UI\Controller\User;

use App\Domain\Achievement\Service\UserAchievementProgressService;
use App\Domain\Achievement\UserAchievementProgress\CompareUserAchievementsUseCase;
use App\Domain\Achievement\UserAchievementProgress\ListUserAchievementProgressesUseCase;
use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Achievement\UserAchievementProgress\CompareUserAchievementsHttp;
use App\UI\Adapters\Http\Achievement\UserAchievementProgress\ListUserAchievementProgressesHttp;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(path: '/users', name: 'user_achievements_')]
final class UserAchievementController extends AbstractController
{
    public function __construct(
        private readonly UserAchievementProgressService $userAchievementProgressService,
        private readonly UserDALInterface               $userDAL,
    ) {
    }

    #[Route(
        path        : '/{userId}/achievements',
        name        : 'detail',
        requirements: [
            'userId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_USER_ACHIEVEMENT_PROGRESS_LIST)]
    #[OAT\Get(
        description: 'Returns achievements for a specific user.',
        summary    : 'Get user details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'User details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'User not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        Request                              $request,
        ListUserAchievementProgressesUseCase $useCase,
        NormalizerInterface                  $normalizer,
        string                               $userId
    ): JsonResponse {
        $user = $this->userDAL->getById(Uuid::fromString($userId));
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Force the return of all results
        $request->query->set('page', 1);
        $request->query->set('limit', -1);

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListUserAchievementProgressesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                ),
                user: $user
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->userAchievementProgressService->transformCollectionToDTO(
            userAchievementProgresses: $paginator->getItems(),
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_ME,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/me/achievements',
        name   : 'me_achievement_scores',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OAT\Get(
        description: 'Returns the achievement scores of the currently authenticated user.',
        summary    : 'Get achievement scores for current user.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'User achievement scores.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'User not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function me(
        Request                              $request,
        ListUserAchievementProgressesUseCase $useCase,
        NormalizerInterface                  $normalizer,
    ): JsonResponse {
        // Force the return of all results
        $request->query->set('page', 1);
        $request->query->set('limit', -1);

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListUserAchievementProgressesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                ),
                user: $this->getUser()
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->userAchievementProgressService->transformCollectionToDTO(
            userAchievementProgresses: $paginator->getItems(),
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_ME,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{userId}/achievements/compare/{otherId}',
        name        : 'compare',
        requirements: [
            'userId'  => '[0-9a-fA-F\-]{36}',
            'otherId' => '[0-9a-fA-F\-]{36}',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_USER_ACHIEVEMENT_PROGRESS_LIST)]
    #[OAT\Get(
        description: 'Compare achievements between two users.',
        summary    : 'Compare user achievements',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter(
                name       : 'userId',
                description: 'Base user UUID',
                in         : 'path',
                required   : true,
                schema     : new OAT\Schema(type: 'string', format: 'uuid')
            ),
            new OAT\Parameter(
                name       : 'otherId',
                description: 'Compared user UUID',
                in         : 'path',
                required   : true,
                schema     : new OAT\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'Comparison result.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'User not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function compare(
        CompareUserAchievementsUseCase $useCase,
        NormalizerInterface            $normalizer,
        string                         $userId,
        string                         $otherId
    ): JsonResponse {
        try {
            $compareUserAchievements = $useCase->execute(
                new CompareUserAchievementsHttp(
                    userId : Uuid::fromString($userId),
                    otherId: Uuid::fromString($otherId),
                ),
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $compareUserAchievements,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
