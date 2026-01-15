<?php

namespace App\Domain\Exercise\Filters;

final class ExerciseFilterMapping
{
    public const FIELD_MAP = [
        'slug'    => 'slug',
        'title'   => 'contents.title',
        'summary' => 'contents.summary',
        'details' => 'contents.details',
    ];
}
