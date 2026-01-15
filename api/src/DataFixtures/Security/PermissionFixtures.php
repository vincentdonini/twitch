<?php

namespace App\DataFixtures\Security;

use App\Domain\Security\Entity\Permission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class PermissionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/security/permissions.json';

        if (!file_exists($jsonFile)) {
            throw new Exception('The JSON file does not exist: ' . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        ksort($data, SORT_STRING);

        foreach ($data as $context => $permissions) {

            if (!is_array($permissions)) {
                throw new Exception("Invalid permissions list for context: $context");
            }

            usort($permissions, function ($a, $b) {
                return strcmp($a['code'], $b['code']);
            });

            foreach ($permissions as $item) {
                if (!isset($item['code'], $item['label'])) {
                    throw new Exception("Permission code or label missing in context: $context");
                }

                $permissionCode = $item['code'];

                $permission = new Permission(
                    $permissionCode,
                    $item['label']
                );
                $permission->setResource($context);

                $manager->persist($permission);

                // Même code pour la référence
                $this->addReference($permissionCode, $permission);
            }
        }

        $manager->flush();
    }
}
