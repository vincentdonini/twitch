<?php

namespace App\Application\Serializer\JsonApi;

use Symfony\Component\Serializer\SerializerInterface;

final class IncludedCollector
{
    private array $included = [];

    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    public function add(string $type, object $entity, array $groups = ['default']): void
    {
        $id = (string)$entity->getId();

        if (!isset($this->included[$type][$id])) {
            $groupsWithDefault = array_merge($groups, ['default']);

            $this->included[$type][$id] = [
                'type'       => $type,
                'id'         => $id,
                'attributes' => $this->serializer->normalize($entity, 'json', ['groups' => $groupsWithDefault]),
            ];
        }
    }

    public function getIncluded(): array
    {
        return array_reduce($this->included, fn($carry, $group) => array_merge($carry, array_values($group)), []);
    }
}
