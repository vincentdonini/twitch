<?php

namespace App\DataFixtures\Security;

use App\Domain\Security\Entity\Permission;
use App\Domain\Security\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class RoleFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/security/roles.json';

        if (!file_exists($jsonFile)) {
            throw new Exception('The JSON file does not exist: ' . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        usort($data, function ($a, $b) {
            return strcmp($a['code'], $b['code']);
        });

        foreach ($data as $item) {
            if (empty($item['code']) || empty($item['label'])) {
                throw new Exception('Role code or label missing in JSON');
            }

            $role = new Role(
                $item['code'],
                $item['label'],
            );

            // ---------------------------------------------------------------------------------------------------------
            // Permissions
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['permissions']) && is_array($item['permissions'])) {
                foreach ($item['permissions'] as $permissionCode) {
                    if (empty($permissionCode)) {
                        throw new Exception('Empty permission code found for role: ' . $item['code']);
                    }

                    $referenceName = $permissionCode;

                    /** @var Permission $permission */
                    $permission = $this->getReference($referenceName, Permission::class);

                    if (!$permission) {
                        throw new Exception('Permission reference not found: ' . $permissionCode);
                    }

                    $role->addPermission($permission);
                }
            }

            $manager->persist($role);

            $this->addReference($role->getCode(), $role);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PermissionFixtures::class,
        ];
    }
}
