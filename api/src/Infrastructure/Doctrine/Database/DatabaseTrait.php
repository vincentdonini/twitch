<?php

namespace App\Infrastructure\Doctrine\Database;

use Doctrine\ORM\EntityManagerInterface;

trait DatabaseTrait
{
    private EntityManagerInterface $entityManager;

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function preSave($entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
    }

    public function refresh($entity): void
    {
        $this->entityManager->refresh($entity);
    }

    public function beginTransaction(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit(): void
    {
        $this->entityManager->commit();
    }

    public function rollback(): void
    {
        $this->entityManager->rollback();
    }
}
