<?php

namespace App\DataFixtures\Organization;

use App\DataFixtures\Geo\CityFixtures;
use App\Domain\Geo\Entity\City;
use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Enum\CompanyStatusEnum;
use App\Domain\Organization\Enum\PlaceStatusEnum;
use App\Domain\Security\Entity\Role;
use App\Domain\User\Entity\User;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    private array $userCache = [];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
//        $jsonPath = __DIR__ . '/../../../default_data/organization/companies.json';
        $jsonPath = __DIR__ . '/../../../default_data/organization/companies_small.json';

        if (!file_exists($jsonPath)) {
            throw new Exception("The JSON file does not exist: " . $jsonPath);
        }

        $companiesData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($companiesData)) {
            throw new Exception("Invalid JSON format in file: " . $jsonPath);
        }

        $cityRepo = $manager->getRepository(City::class);
        $userRepo = $manager->getRepository(User::class);
        $roleRepo = $manager->getRepository(Role::class);

        usort($companiesData, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $batchSize = 1000;
        $i         = 0;

        foreach ($companiesData as $companyData) {
            $city = isset($companyData['city'])
                ? $cityRepo->findOneBy(['slug' => StringHelper::slugify($companyData['city'])])
                : throw new Exception("City not provided for company: " . ($companyData['name'] ?? 'unknown'));

            if (!$city) {
                throw new Exception("City not found in database: " . $companyData['city']);
            }

            $company = new Company(
                slug      : StringHelper::slugify(($companyData['name'])),
                name      : $companyData['name'],
                legalName : $companyData['legalName'] ?? $companyData['name'],
                address   : $companyData['address'],
                postalCode: $companyData['postalCode'],
                city      : $city,
            );

            $company->setSiren($companyData['siren'] ?? null);
            $company->setVatNumber($companyData['vatNumber'] ?? null);
            $company->setLegalForm($companyData['legalForm'] ?? null);
            $company->setActivityCode($companyData['activityCode'] ?? null);
            $company->setAddress2($companyData['address2'] ?? null);
            $company->setPhone(isset($companyData['phone']) ? str_replace(' ', '', $companyData['phone']) : null);
            $company->setEmail($companyData['email'] ?? null);
            $company->setRegistrationDate(isset($companyData['registrationDate']) ? new \DateTimeImmutable($companyData['registrationDate']) : null);
            $company->setStatus(CompanyStatusEnum::ACTIVE);

            $manager->persist($company);

            // Owners
            // ---------------------------------------------------------------------------------------------------------
            $fallbackEmail = $companyData['places'][0]['email'] ?? null;

            foreach ($companyData['owners'] ?? [] as $ownerData) {

                $email = strtolower($ownerData['email'] ?? $fallbackEmail ?? str_replace(' ', '.', $ownerData['name']) . '@example.com.' . rand());

                // Cache local
                if (!isset($this->userCache[$email])) {
                    $user = $userRepo->findOneBy(['email' => $email]);

                    if (!$user) {
                        $names = explode(' ', $ownerData['name'], 2);

                        $user = new User(
                            email    : $email,
                            firstName: $names[0],
                            lastName : $names[1] ?? ''
                        );

                        $user->setPassword($this->passwordHasher->hashPassword($user, 'ChangeMe123!'));

                        $role = $roleRepo->findOneBy(['code' => 'ROLE_OWNER']);
                        if ($role) {
                            $user->addRole($role);
                        }

                        $manager->persist($user);
                    }

                    $this->userCache[$email] = $user;
                }

                $company->addOwner($this->userCache[$email]);
            }

            // Places
            // ---------------------------------------------------------------------------------------------------------
            foreach ($companyData['places'] ?? [] as $placeData) {

                $city = isset($placeData['city'])
                    ? $cityRepo->findOneBy(['slug' => StringHelper::slugify($placeData['city'])])
                    : throw new Exception("City not provided for company: " . ($placeData['name'] ?? 'unknown'));

                if (!$city) {
                    throw new Exception("City not found in database: " . $placeData['city']);
                }

                $place = new Place(
                    company   : $company,
                    slug      : StringHelper::slugify(($placeData['name'])),
                    name      : $placeData['name'],
                    legalName : $placeData['legalName'] ?? $placeData['name'],
                    address   : $placeData['address'],
                    postalCode: $placeData['postalCode'],
                    city      : $city,
                );

                $place->setSiret($placeData['siret'] ?? null);
                $place->setAddress2($placeData['address2'] ?? null);
                $place->setNbDaysBeforeReservation($placeData['nbDaysBeforeReservation'] ?? null);
                $place->setNbHoursBeforeCancelReservation($placeData['nbHoursBeforeCancelReservation'] ?? null);
                $place->setPhone(isset($placeData['phone']) ? str_replace(' ', '', $placeData['phone']) : null);
                $place->setEmail($placeData['email'] ?? null);
                $place->setWebsite($placeData['website'] ?? null);
                $place->setRegistrationDate(isset($placeData['registrationDate']) ? new \DateTimeImmutable($placeData['registrationDate']) : null);
                $place->setStatus(PlaceStatusEnum::ACTIVE);

                foreach ($company->getOwners() as $owner) {
                    $place->addUser($owner);
                }

                $company->addPlace($place);

                $manager->persist($place);
            }

            // Staff
            // ---------------------------------------------------------------------------------------------------------
            foreach ($companyData['staff'] ?? [] as $staffData) {
                $email = rand() . '@example.com';
                $names = explode(' ', $staffData['name'], 2);

                $user = new User(
                    email    : $email,
                    firstName: $names[0],
                    lastName : $names[1] ?? '',
                );

                // Mot de passe
                $user->setPassword($this->passwordHasher->hashPassword($user, 'ChangeMe123!'));

                // Assignation du rôle
                $role = $roleRepo->findOneBy(['code' => 'ROLE_' . strtoupper($staffData['role'])]);
                if ($role) $user->addRole($role);

                $manager->persist($user);

                // Relation staff ↔ places (ATTENTION : pas owners)
                foreach ($company->getPlaces() as $place) {
                    $place->addUser($user);
                }
            }

            $i++;

            if ($i % $batchSize === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}
