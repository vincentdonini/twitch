<?php

namespace App\UI\Controller\Organization;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\Organization\Service\SubscriptionService;
use App\Domain\Organization\Subscription\CancelSubscriptionUseCase;
use App\Domain\Organization\Subscription\CreateSubscriptionUseCase;
use App\Domain\Organization\Subscription\GetSubscriptionByIdUseCase;
use App\Domain\Organization\Subscription\ListSubscriptionsByPlaceIdUseCase;
use App\Domain\Organization\Subscription\UpdateSubscriptionUseCase;
use App\Domain\User\Entity\User;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Security\Voters\Subscription\SubscriptionCreateVoter;
use App\Infrastructure\Security\Voters\Subscription\SubscriptionViewVoter;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Organization\Subscription\CancelSubscriptionHttp;
use App\UI\Adapters\Http\Organization\Subscription\CreateSubscriptionHttp;
use App\UI\Adapters\Http\Organization\Subscription\GetSubscriptionByIdHttp;
use App\UI\Adapters\Http\Organization\Subscription\ListSubscriptionsByPlaceIdHttp;
use App\UI\Adapters\Http\Organization\Subscription\UpdateSubscriptionHttp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(
    path        : '/places/{placeId}/formulas/{formulaId}/subscriptions',
    name        : 'subscription_',
    requirements: [
        'placeId'   => '[0-9a-fA-F\-]+',
        'formulaId' => '[0-9a-fA-F\-]+',
    ],
)]
final class SubscriptionController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly PlaceDALInterface   $placeDAL,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_SUBSCRIPTION_LIST)]
    public function list(
        Request                           $request,
        ListSubscriptionsByPlaceIdUseCase $useCase,
        NormalizerInterface               $normalizer,
        string                            $placeId,
        string                            $formulaId,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListSubscriptionsByPlaceIdHttp(
                    placeId  : Uuid::fromString($placeId),
                    formulaId: Uuid::fromString($formulaId),
                    page     : $paginatorValues->getPage(),
                    limit    : $paginatorValues->getLimit(),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->subscriptionService->transformCollectionToDTO(
            subscriptions: $paginator->getItems(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [FrontGroupsEnum::SUBSCRIPTION_LIST],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(
                paginator      : $paginator,
                paginatorValues: $paginatorValues
            )
        );
    }

    #[Route(
        path        : '/{subscriptionId}',
        name        : 'detail',
        requirements: [
            'subscriptionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_SUBSCRIPTION_VIEW)]
    public function detail(
        GetSubscriptionByIdUseCase $useCase,
        NormalizerInterface        $normalizer,
        string                     $placeId,
        string                     $formulaId,
        string                     $subscriptionId,
    ): JsonResponse {
        try {
            $subscription = $useCase->execute(
                new GetSubscriptionByIdHttp(
                    id       : Uuid::fromString($subscriptionId),
                    placeId  : Uuid::fromString($placeId),
                    formulaId: Uuid::fromString($formulaId),
                )
            );

            if (!$this->isGranted(SubscriptionViewVoter::VIEW, $subscription->getPlace())) {
                throw new AccessDeniedException();
            }
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->subscriptionService->transformToDTO($subscription);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [FrontGroupsEnum::SUBSCRIPTION_DETAIL],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '',
        name   : 'create',
        methods: ['POST']
    )]
    public function create(
        CreateSubscriptionUseCase $useCase,
        Request                   $request,
        NormalizerInterface       $normalizer,
        string                    $placeId,
        string                    $formulaId,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true) ?? [];

            /** @var User $authenticatedUser */
            $authenticatedUser = $this->getUser();
            if (!$authenticatedUser) {
                throw new AuthenticationException('No authenticated user.');
            }

            $targetUserId = isset($payload['userId'])
                ? Uuid::fromString($payload['userId'])
                : $authenticatedUser->getId();

            $place = $this->placeDAL->getById(Uuid::fromString($placeId));
            if (!$place) {
                throw new EntityNotFoundException('Place not found.');
            }

            if (!$this->isGranted(SubscriptionCreateVoter::CREATE, $place)) {
                throw new AccessDeniedException('Access denied.');
            }

            if (!$targetUserId->equals($authenticatedUser->getId())
                && !$this->isGranted(ListPermissions::PERMISSION_SUBSCRIPTION_CREATE_FOR_USER)
            ) {
                throw new AccessDeniedException('Access denied.');
            }

            $status = $targetUserId->equals($authenticatedUser->getId())
                ? SubscriptionStatusEnum::ACTIVE
                : SubscriptionStatusEnum::PENDING;

            $subscription = $useCase->execute(
                new CreateSubscriptionHttp(
                    placeId  : Uuid::fromString($placeId),
                    formulaId: Uuid::fromString($formulaId),
                    userId   : $targetUserId,
                    status   : $status,
                    payload  : $payload
                )
            );

            $dtoItem = $this->subscriptionService->transformToDTO($subscription);

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $dtoItem,
                    format : 'json',
                    context: [
                        'groups' => [FrontGroupsEnum::SUBSCRIPTION_MANAGE],
                    ]
                ),
                status : Response::HTTP_CREATED,
                headers: [
                    'X-RESOURCE-ID' => $subscription->getId()->toRfc4122(),
                ]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{subscriptionId}',
        name        : 'update',
        requirements: [
            'subscriptionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_SUBSCRIPTION_MANAGE)]
    public function update(
        UpdateSubscriptionUseCase $useCase,
        Request                   $request,
        string                    $placeId,
        string                    $formulaId,
        string                    $subscriptionId,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $authenticatedUser = $this->getUser();
            if (!$authenticatedUser) {
                throw new AuthenticationException();
            }

            $targetUserId = isset($payload['userId'])
                ? Uuid::fromString($payload['userId'])
                : $authenticatedUser->getId();

            if (
                !$this->isGranted(ListPermissions::PERMISSION_SUBSCRIPTION_CREATE_FOR_USER)
                && !$targetUserId->equals($authenticatedUser->getId())
            ) {
                throw new AccessDeniedException();
            }

            $useCase->execute(
                new UpdateSubscriptionHttp(
                    placeId       : Uuid::fromString($placeId),
                    formulaId     : Uuid::fromString($formulaId),
                    subscriptionId: Uuid::fromString($subscriptionId),
                    userId        : $targetUserId,
                    payload       : $payload
                ),
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{subscriptionId}/cancel',
        name        : 'cancel',
        requirements: [
            'subscriptionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PATCH']
    )]
    public function cancel(
        CancelSubscriptionUseCase $useCase,
        Request                   $request,
        string                    $subscriptionId,
    ): Response {
        try {
            $payload = json_decode($request->getContent(), true) ?? [];

            /** @var User $authenticatedUser */
            $authenticatedUser = $this->getUser();
            if (!$authenticatedUser) {
                throw new AuthenticationException('No authenticated user.');
            }

            $useCase->execute(
                new CancelSubscriptionHttp(
                    subscriptionId     : Uuid::fromString($subscriptionId),
                    authenticatedUserId: $authenticatedUser->getId(),
                    payload            : $payload
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
