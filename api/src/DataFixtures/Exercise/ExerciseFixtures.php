<?php

namespace App\DataFixtures\Exercise;

use App\DataFixtures\Equipment\EquipmentFixtures;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Entity\ExerciseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ExerciseFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/exercise/exercises.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {
            $exercise = new Exercise();
            $exercise->setSlug($item['slug']);

            if (isset($item['equipment'])) {
                $equipment = $manager->getRepository(Equipment::class)->findOneBy(['slug' => $item['equipment']]);
                if (!$equipment) {
                    throw new Exception("Equipment not found: " . $item['equipment']);
                }
                $exercise->setEquipment($equipment);
            } else {
                $exercise->setEquipment(null);
            }

            $exerciseCategory = $manager->getRepository(ExerciseCategory::class)->findOneBy(['slug' => $item['category']]);
            if (!$exerciseCategory) {
                throw new Exception("Category not found: " . $item['category']);
            }
            $exercise->setExerciseCategory($exerciseCategory);

            $manager->persist($exercise);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExerciseCategoryFixtures::class,
            EquipmentFixtures::class,
        ];
    }
}
