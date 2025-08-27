<?php

namespace App\Infrastructure\Persistence\Doctrine\Event;

use App\Application\Event\DTO\TopDonorDTO;
use App\Domain\Event\Entity\Event;
use App\Domain\Event\Repository\EventRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Event::class);
    }

    public function findAllEvents(): array
    {
        return $this->findAll();
    }

    public function findOneById(string $id): ?Event
    {
        return $this->find($id);
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
        ?array  $typesArray = null,
    ): array {
        $qb = $this->createQueryBuilder('e');

        // Apply dynamic sorting
        $validSortFields = ['id', 'type', 'userId', 'username', 'occurredAt'];
        if ($sortBy && in_array($sortBy, $validSortFields)) {
            $qb->orderBy("e.$sortBy", $sortOrder === 'desc' ? 'DESC' : 'ASC');
        }

        // Filter by types
        if ($typesArray) {
            $qb->andWhere('e.type IN (:types)')
                ->setParameter('types', $typesArray);
        }

        // Pagination
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws Exception
     */
    public function findTopDonor(): ?TopDonorDTO
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT
                sub.user_id,
                sub.username,
                total.totalAmount
            FROM (
                SELECT user_id, MAX(occurred_at) AS last_seen
                FROM events
                WHERE type = 'cheer'
                GROUP BY user_id
            ) AS latest
            JOIN events sub
                ON sub.user_id = latest.user_id AND sub.occurred_at = latest.last_seen
            JOIN (
                SELECT user_id, SUM(amount) AS totalAmount
                FROM events
                WHERE type = 'cheer'
                GROUP BY user_id
            ) AS total
                ON sub.user_id = total.user_id
            ORDER BY total.totalAmount DESC
            LIMIT 1
        SQL;

        $result = $conn->executeQuery($sql)->fetchAssociative();

        return $result ? TopDonorDTO::fromArray($result) : null;
    }

    public function findLastDonor(): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.type = :type')
            ->setParameter('type', 'cheer')
            ->orderBy('e.occurredAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastFollow(): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.type = :type')
            ->setParameter('type', 'follow')
            ->orderBy('e.occurredAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastSubscription(): ?Event
    {
        return $this->createQueryBuilder('e')
            ->where('e.type IN (:types)')
            ->setParameter('types', ['subscription', 'resub'])
            ->orderBy('e.occurredAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Event[]
     */
    public function findActiveSubscriptions(): array
    {
        $dateLimit = new DateTimeImmutable('-1 month');

        return $this->createQueryBuilder('e')
            ->where('e.type IN (:types)')
            ->andWhere('e.occurredAt >= :dateLimit')
            ->setParameter('types', ['subscription', 'resub'])
            ->setParameter('dateLimit', $dateLimit)
            ->orderBy('e.occurredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Event $event): void
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function delete(Event $event): void
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }
}
