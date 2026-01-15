<?php

namespace App\Infrastructure\Doctrine\Database;

use App\Domain\Core\Ports\DatabaseInterface;
use Doctrine\Persistence\ManagerRegistry;

final class DatabaseAdapter implements DatabaseInterface
{
    use DatabaseTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
    ) {
        $this->entityManager = $managerRegistry->getManager('default');
    }
}
