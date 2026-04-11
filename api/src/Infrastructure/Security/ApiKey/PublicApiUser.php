<?php

namespace App\Infrastructure\Security\ApiKey;

use App\Infrastructure\Security\Voters\ListPermissions;
use Symfony\Component\Security\Core\User\UserInterface;

final class PublicApiUser implements UserInterface
{
    public const IDENTIFIER = 'public_api';

    /**
     * Permissions granted to anonymous users authenticated via public API key.
     * Only read (LIST/VIEW) access on public resources.
     */
    public const PUBLIC_PERMISSIONS = [

        // Benchmarks
        ListPermissions::PERMISSION_BENCHMARK_LIST,
        ListPermissions::PERMISSION_BENCHMARK_VIEW,

        // Equipments
        ListPermissions::PERMISSION_EQUIPMENT_LIST,
        ListPermissions::PERMISSION_EQUIPMENT_VIEW,

        // Exercises
        ListPermissions::PERMISSION_EXERCISE_LIST,
        ListPermissions::PERMISSION_EXERCISE_VIEW,
        ListPermissions::PERMISSION_EXERCISE_CATEGORY_LIST,
        ListPermissions::PERMISSION_EXERCISE_CATEGORY_VIEW,

        // Muscles
        ListPermissions::PERMISSION_MUSCLE_LIST,
        ListPermissions::PERMISSION_MUSCLE_VIEW,

        // Stats
        ListPermissions::PERMISSION_STATS_VIEW,

        // WODs
        ListPermissions::PERMISSION_WOD_LIST,
        ListPermissions::PERMISSION_WOD_VIEW,
        ListPermissions::PERMISSION_WOD_CATEGORY_LIST,
        ListPermissions::PERMISSION_WOD_CATEGORY_VIEW,
        ListPermissions::PERMISSION_WOD_TYPE_LIST,
        ListPermissions::PERMISSION_WOD_TYPE_VIEW,
        ListPermissions::PERMISSION_WOD_DIVISION_LIST,
        ListPermissions::PERMISSION_WOD_DIVISION_VIEW,
        ListPermissions::PERMISSION_WOD_AGE_RANGE_LIST,
        ListPermissions::PERMISSION_WOD_AGE_RANGE_VIEW,
        ListPermissions::PERMISSION_WOD_SCORE_LIST,
    ];

    public function getRoles(): array
    {
        return ['ROLE_PUBLIC_API'];
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
