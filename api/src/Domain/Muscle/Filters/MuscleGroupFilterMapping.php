<?php

namespace App\Domain\Muscle\Filters;

final class MuscleGroupFilterMapping
{
    public const FIELD_MAP = [
        'slug'    => 'slug',
        'title'   => 'contents.title',
        'summary' => 'contents.summary',
        'details' => 'contents.details',
    ];
}
