<?php

namespace App\Domain\Core\Ports;

interface DatabaseInterface
{
    public function save(): void;

    public function preSave($entity): void;

    public function remove($entity): void;

    public function refresh($entity): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}