<?php

namespace App\DataFixtures\Muscle;

use App\Domain\Content\Entity\ContentMuscleSegment;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleSegment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MuscleSegmentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/muscle/muscleSegments.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {
            $muscle = $manager->getRepository(Muscle::class)->findOneBy(['slug' => $item['muscle']]);
            if (!$muscle) {
                throw new Exception("Muscle not found: " . $item['muscle']);
            }

            $segment = new MuscleSegment(
                slug  : $item['slug'],
                muscle: $muscle,
            );

            $manager->persist($segment);

            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentMuscleSegment(
                        muscleSegment: $segment,
                        locale       : $locale,
                        title        : $contentData['title'],
                        summary      : $contentData['summary'],
                        details      : $contentData['details'] ?: null,
                    );

                    $manager->persist($content);
                    $segment->addContent($content);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MuscleFixtures::class,
        ];
    }
}
