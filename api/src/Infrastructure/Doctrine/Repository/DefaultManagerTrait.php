<?php

namespace App\Infrastructure\Doctrine\Repository;

trait DefaultManagerTrait
{
    protected function getManager(): string{
        return 'default';
    }
}
