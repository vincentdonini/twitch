<?php

namespace App\DataFixtures\Geo;

use App\Domain\Geo\Entity\Department;
use App\Domain\Geo\Entity\Region;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class DepartmentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/geo/department.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        $regionRepo = $manager->getRepository(Region::class);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        usort($data, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $regions = $regionRepo->findAll();
        $regionsByName = [];
        foreach ($regions as $dep) {
            $regionsByName[$dep->getName()] = $dep;
        }

        foreach ($data as $departmentData) {
            $regionName = $departmentData['region'] ?? null;
            $region = $regionsByName[$regionName]
                ?? throw new Exception("Region not found - $regionName");

            $department = new Department(
                code  : $departmentData['code'],
                slug  : StringHelper::slugify(($departmentData['name'])),
                name  : $departmentData['name'],
                region: $region
            );

            $manager->persist($department);
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
            RegionFixtures::class,
        ];
    }
}
