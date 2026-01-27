<?php

namespace App\DataFixtures\Geo;

use App\Domain\Geo\Entity\Country;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CountryFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/geo/country.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        $collator = collator_create('fr_FR');
        usort($data, function ($a, $b) use ($collator) {
            return $collator->compare($a['name'], $b['name']);
        });

        foreach ($data as $countryData) {
            $country = new Country(
                slug         : StringHelper::slugify(($countryData['name'])),
                name         : $countryData['name'],
                defaultLocale: $countryData['default_locale']
            );

            $manager->persist($country);
        }

        $manager->flush();
        $manager->clear();
    }
}
