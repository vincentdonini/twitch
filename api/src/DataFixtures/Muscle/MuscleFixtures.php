<?php

namespace App\DataFixtures\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Entity\MuscleArea;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MuscleFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/muscle/muscles.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {
            $muscle = new Muscle();
            $muscle->setSlug($item['slug']);

            $area = $manager->getRepository(MuscleArea::class)->findOneBy(['slug' => $item['area']]);
            if (!$area) {
                throw new Exception("Muscle area not found: " . $item['area']);
            }
            $muscle->setArea($area);

            if (isset($item['group'])) {
                $group = $manager->getRepository(MuscleGroup::class)->findOneBy(['slug' => $item['group']]);
                if (!$group) {
                    throw new Exception("Muscle group not found: " . $item['group']);
                }
                $muscle->setGroup($group);
            } else {
                $muscle->setGroup(null);
            }

            $manager->persist($muscle);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MuscleAreaFixtures::class,
            MuscleGroupFixtures::class,
        ];
    }
}
