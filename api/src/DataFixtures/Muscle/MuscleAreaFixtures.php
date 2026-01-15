<?php

namespace App\DataFixtures\Muscle;

use App\Domain\Content\Entity\ContentMuscleArea;
use App\Domain\Muscle\Entity\MuscleArea;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MuscleAreaFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/muscle/muscleAreas.json';

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
            $muscleArea = new MuscleArea();
            $muscleArea->setSlug($item['slug']);

            // Gestion des contenus
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentMuscleArea(
                        muscleArea: $muscleArea,
                        locale    : $locale,
                        title     : $contentData['title'],
                        summary   : $contentData['summary'],
                        details   : $contentData['details'],
                    );

                    $manager->persist($content);
                    $muscleArea->addContent($content);
                }
            }

            $manager->persist($muscleArea);
        }

        $manager->flush();
    }
}
