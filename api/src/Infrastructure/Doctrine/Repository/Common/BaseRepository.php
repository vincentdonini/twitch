<?php

namespace App\Infrastructure\Doctrine\Repository\Common;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @template T
 */
abstract class BaseRepository extends ServiceEntityRepository
{
    protected ObjectManager $entityManager;
    protected string $entityClass;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $this->entityManager = $registry->getManager();
        $this->entityClass   = $entityClass;

        parent::__construct($registry, $entityClass);
    }

    /**
     * @return T|null
     */
    public function findOneById(string $id): ?object
    {
        return $this->find($id);
    }

    /**
     * @return T[]
     */
    public function findAllEntities(): array
    {
        return $this->createQueryBuilder($this->getAlias())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return T[]
     */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int $offset,
        int $limit,
        ?string $search = null
    ): array {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy($this->getAlias() . ".$sortBy", $sortOrder);

        if ($search) {
            $searchable = $this->getSearchableFields();
            $orX = $qb->expr()->orX();
            $i = 0;
            foreach ($searchable as $field => $type) {
                $param = 'search' . $i;
                if ($type === 'like') {
                    $orX->add($qb->expr()->like($this->getAlias() . ".$field", ':' . $param));
                    $qb->setParameter($param, "%$search%");
                } else {
                    $orX->add($qb->expr()->eq($this->getAlias() . ".$field", ':' . $param));
                    $qb->setParameter($param, $search);
                }
                $i++;
            }
            $qb->andWhere($orX);
        }

        return $qb->getQuery()->getResult();
    }

    public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function countAll(?string $search = null): int
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->select("COUNT({$this->getAlias()}.id)");

        if ($search) {
            $searchable = $this->getSearchableFields();
            $orX = $qb->expr()->orX();
            $i = 0;
            foreach ($searchable as $field => $type) {
                $param = 'search' . $i;
                if ($type === 'like') {
                    $orX->add($qb->expr()->like($this->getAlias() . ".$field", ':' . $param));
                    $qb->setParameter($param, "%$search%");
                } else {
                    $orX->add($qb->expr()->eq($this->getAlias() . ".$field", ':' . $param));
                    $qb->setParameter($param, $search);
                }
                $i++;
            }
            $qb->andWhere($orX);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return T[]
     */
    public function findPaginated(int $offset, int $limit, ?string $sortBy = null, ?string $sortOrder = null, ?string $search = null): array
    {
        $sortBy    ??= 'id';
        $sortOrder ??= 'asc';

        return $this->findByFilters($sortBy, $sortOrder, $offset, $limit, $search);
    }

    abstract protected function getAlias(): string;

    /**
     * @return array<string, 'like'|'eq'>
     */
    abstract protected function getSearchableFields(): array;

    /**
     * @return string[]
     */
    abstract protected function getSortableFields(): array;
}
