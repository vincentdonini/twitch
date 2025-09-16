<?php

namespace App\DataFixtures\Movement;

use App\DataFixtures\Equipment\EquipmentFixtures;
use App\DataFixtures\Muscle\MuscleFixtures;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Movement\Entity\Movement;
use App\Domain\Movement\Entity\MovementCategory;
use App\Domain\Muscle\Entity\Muscle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MovementFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/movement/movements.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        usort($data, function ($a, $b) {
            return strcmp($a['slug'], $b['slug']);
        });

        foreach ($data as $item) {
            $movement = new Movement();
            $movement->setSlug($item['slug']);

            $movementCategory = $manager->getRepository(MovementCategory::class)->findOneBy(['slug' => $item['category']]);
            if (!$movementCategory) {
                throw new Exception("Category not found: " . $item['category']);
            }
            $movement->setMovementCategory($movementCategory);

            if (isset($item['equipment'])) {
                $equipment = $manager->getRepository(Equipment::class)->findOneBy(['slug' => $item['equipment']]);
                if (!$equipment) {
                    throw new Exception("Equipment not found: " . $item['equipment']);
                }
                $movement->setEquipment($equipment);
            } else {
                $movement->setEquipment(null);
            }

            if (!empty($item['muscles'])) {
                foreach ($item['muscles'] as $muscleId) {
                    $muscle = $manager->getRepository(Muscle::class)->find($muscleId);
                    if ($muscle) {
                        $movement->addMuscle($muscle);
                    } else {
                        throw new Exception("Muscle with ID $muscleId not found");
                    }
                }
            }

            $manager->persist($movement);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MovementCategoryFixtures::class,
            EquipmentFixtures::class,
            MuscleFixtures::class,
        ];
    }
}
