<?php

namespace App\DataFixtures\Geo;

use App\Domain\Geo\Entity\City;
use App\Domain\Geo\Entity\Department;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/geo/city.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        $departmentRepo = $manager->getRepository(Department::class);

        $collator = collator_create('fr_FR');
        usort($data, fn($a, $b) => $collator->compare($a['name'], $b['name']));

        $departments       = $departmentRepo->findAll();
        $departmentsByName = [];
        foreach ($departments as $dep) {
            $departmentsByName[$dep->getName()] = $dep;
        }

        foreach ($data as $cityData) {

            $departmentName = $cityData['department'] ?? null;
            $department     = $departmentsByName[$departmentName]
                ?? throw new Exception("Department not found - $departmentName");

            // --- FIX ZIP CODES ---
            $postalCodes = $cityData['zip_codes'] ?? null;

            if (is_string($postalCodes)) {
                $postalCodes = array_map('trim', explode(',', $postalCodes));
            } elseif (is_int($postalCodes)) {
                $postalCodes = [(string)$postalCodes];
            } elseif (!is_array($postalCodes)) {
                $postalCodes = [];
            }

            $city = new City(
                slug       : StringHelper::slugify($cityData['name']),
                name       : $cityData['name'],
                postalCodes: $postalCodes,
                coordinates: $cityData['coordinates'] ?? null,
                department : $department
            );

            $city->setInseeCode($cityData['insee_code'] ?? null);
            $city->setPopulation($cityData['population'] ?? null);
            $city->setArea($cityData['area'] ?? null);

            $manager->persist($city);
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
            RegionFixtures::class,
            DepartmentFixtures::class,
        ];
    }
}
