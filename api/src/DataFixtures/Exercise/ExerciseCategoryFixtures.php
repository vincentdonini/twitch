<?php

namespace App\DataFixtures\Exercise;

use App\Domain\Exercise\Entity\ExerciseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ExerciseCategoryFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/exercise/categories.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {
            $exerciseCategory = new ExerciseCategory();
            $exerciseCategory->setSlug($item['slug']);

            $manager->persist($exerciseCategory);
        }

        $manager->flush();
    }
}
