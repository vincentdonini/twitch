<?php

namespace App\DataFixtures\Geo;

use App\Domain\Geo\Entity\Country;
use App\Domain\Geo\Entity\Region;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class RegionFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/geo/region.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        $countryRepo = $manager->getRepository(Country::class);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        $collator = collator_create('fr_FR');
        usort($data, function ($a, $b) use ($collator) {
            return $collator->compare($a['name'], $b['name']);
        });

        $countries = $countryRepo->findAll();
        $countriesByName = [];
        foreach ($countries as $country) {
            $countriesByName[$country->getName()] = $country;
        }

        foreach ($data as $regionData) {
            $countryName = $regionData['country'] ?? null;
            $country = $countriesByName[$countryName]
                ?? throw new Exception("Region not found - $countryName");

            $region = new Region(
                code   : $regionData['code'],
                slug   : StringHelper::slugify(($regionData['name'])),
                name   : $regionData['name'],
                country: $country
            );

            $manager->persist($region);
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }
}
