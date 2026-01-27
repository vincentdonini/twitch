<?php

namespace App\Domain\Organization\Place;

final class ImportUsersForPlaceResult
{
    public function __construct(
        public int   $created,
        public int   $existing,
        public int   $attached,
        public array $errors,
    ) {
    }
}
