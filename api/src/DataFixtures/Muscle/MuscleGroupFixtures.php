<?php

namespace App\DataFixtures\Muscle;

use App\Domain\Content\Entity\ContentMuscleGroup;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Entity\MuscleGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MuscleGroupFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/muscle/muscleGroups.json';

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
            $muscleGroup = new MuscleGroup();
            $muscleGroup->setSlug($item['slug']);

            $muscleArea = $manager->getRepository(MuscleArea::class)->findOneBy(['slug' => $item['area']]);
            if (!$muscleArea) {
                throw new Exception("Muscle area not found: " . $item['area']);
            }
            $muscleGroup->setMuscleArea($muscleArea);

            // Gestion des contenus
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentMuscleGroup(
                        muscleGroup: $muscleGroup,
                        locale     : $locale,
                        title      : $contentData['title'],
                        summary    : $contentData['summary'],
                        details    : $contentData['details'],
                    );

                    $manager->persist($content);
                    $muscleGroup->addContent($content); // pour la collection côté MuscleGroup
                }
            }

            $manager->persist($muscleGroup);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MuscleAreaFixtures::class,
        ];
    }
}
