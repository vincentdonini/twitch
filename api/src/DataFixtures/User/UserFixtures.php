<?php

namespace App\DataFixtures\User;

use App\DataFixtures\Security\RoleFixtures;
use App\Domain\Security\Entity\Role;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/users.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {

            // ---------------------------------------------------------------------------------------------------------
            // USER
            // ---------------------------------------------------------------------------------------------------------
            $user = new User(
                email    : $item['email'],
                firstName: $item['firstName'],
                lastName : $item['lastName'],
            );

            // Password
            // ---------------------------------------------------------------------------------------------------------
            $hashedPassword = $this->passwordHasher->hashPassword($user, $item['password']);
            $user->setPassword($hashedPassword);

            // Roles
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['roles'])) {
                foreach ($item['roles'] as $roleCode) {
                    $referenceName = $roleCode;

                    /** @var Role $role */
                    $role = $this->getReference($referenceName, Role::class);

                    if (!$role) {
                        throw new Exception("Role not found: $roleCode");
                    }

                    $user->addRole($role);
                }
            } else {
                $referenceName = 'ROLE_USER';

                /** @var Role $role */
                $role = $this->getReference($referenceName, Role::class);
                if ($role) {
                    $user->addRole($role);
                }
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}
