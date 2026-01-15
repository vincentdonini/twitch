<?php

namespace App\Domain\Muscle\Filters;

final class MuscleAreaFilterMapping
{
    public const FIELD_MAP = [
        'slug'    => 'slug',
        'title'   => 'contents.title',
        'summary' => 'contents.summary',
        'details' => 'contents.details',
    ];
}
