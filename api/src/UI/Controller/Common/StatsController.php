<?php

namespace App\UI\Controller\Common;

use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\User\Ports\UserDALInterface;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/stats', name: 'stats_')]
final class StatsController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly WodDALInterface   $wodRepository,
        private readonly PlaceDALInterface $placeRepository,
        private readonly UserDALInterface  $userRepository,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'public',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_STATS_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return global platform statistics.',
        summary    : 'Platform stats.',
        security   : [],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Platform statistics.',
                content    : new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'wodCount',     type: 'integer'),
                        new OAT\Property(property: 'placeCount',   type: 'integer'),
                        new OAT\Property(property: 'athleteCount', type: 'integer'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function stats(): JsonResponse
    {
        try {
            return new JsonResponse([
                'wodCount'     => $this->wodRepository->countWods(),
                'placeCount'   => $this->placeRepository->countPlaces(),
                'athleteCount' => $this->userRepository->countUsers(),
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
