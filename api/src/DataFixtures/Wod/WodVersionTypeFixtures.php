<?php

namespace App\DataFixtures\Wod;

use App\Domain\Wod\Entity\WodVersionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodVersionTypeFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/versionTypes.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {
            $wodVersionType = new WodVersionType();
            $wodVersionType->setSlug($item['slug']);

            $manager->persist($wodVersionType);
        }

        $manager->flush();
    }
}
