<?php

namespace App\DataFixtures\Muscle;

use App\Domain\Content\Entity\ContentMuscle;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleGroup;
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

        usort($data, function ($a, $b) {
            return strcmp($a['slug'], $b['slug']);
        });

        foreach ($data as $item) {
            $muscleGroup = null;
            if (isset($item['group'])) {
                $muscleGroup = $manager->getRepository(MuscleGroup::class)->findOneBy(['slug' => $item['group']]);
                if (!$muscleGroup) {
                    throw new Exception("Muscle group not found: " . $item['group']);
                }
            }

            if (!$muscleGroup) {
                throw new Exception("Muscle must belong to a group: " . $item['slug']);
            }

            $muscle = new Muscle(
                slug      : $item['slug'],
                muscleArea: $muscleGroup->getMuscleArea(),
            );

            $muscle->setMuscleGroup($muscleGroup);

            $manager->persist($muscle);

            // Contents
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentMuscle(
                        muscle : $muscle,
                        locale : $locale,
                        title  : $contentData['title'],
                        summary: $contentData['summary'],
                        details: $contentData['details'],
                    );

                    $manager->persist($content);
                    $muscle->addContent($content);
                }
            }
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
