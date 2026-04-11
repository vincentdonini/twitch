<?php

namespace App\DataFixtures\Exercise;

use App\DataFixtures\Equipment\EquipmentFixtures;
use App\DataFixtures\Muscle\MuscleFixtures;
use App\DataFixtures\Muscle\MuscleSegmentFixtures;
use App\Domain\Content\Entity\ContentExercise;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleSegment;
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

        usort($data, function ($a, $b) {
            return strcmp($a['slug'], $b['slug']);
        });

        foreach ($data as $item) {
            $exerciseCategory = $manager->getRepository(ExerciseCategory::class)->findOneBy(['slug' => $item['category']]);
            if (!$exerciseCategory) {
                throw new Exception("Category not found: " . $item['category']);
            }

            $exercise = new Exercise(
                slug            : $item['slug'],
                exerciseCategory: $exerciseCategory
            );

            if (isset($item['equipment'])) {
                $equipment = $manager->getRepository(Equipment::class)->findOneBy(['slug' => $item['equipment']]);
                if (!$equipment) {
                    throw new Exception("Equipment not found: " . $item['equipment']);
                }
                $exercise->setEquipment($equipment);
            } else {
                $exercise->setEquipment(null);
            }

            if (!empty($item['muscles'])) {
                foreach ($item['muscles'] as $muscleSlug) {
                    $muscle = $manager->getRepository(Muscle::class)->findOneBy(['slug' => $muscleSlug]);
                    if (!$muscle) {
                        throw new Exception("Muscle with slug '$muscleSlug' not found");
                    }
                    $exercise->addMuscle($muscle);
                }
            }

            if (!empty($item['muscleSegments'])) {
                foreach ($item['muscleSegments'] as $segmentSlug) {
                    $segment = $manager->getRepository(MuscleSegment::class)->findOneBy(['slug' => $segmentSlug]);
                    if (!$segment) {
                        throw new Exception("MuscleSegment with slug '$segmentSlug' not found");
                    }
                    $exercise->addMuscleSegment($segment);
                }
            }

            $manager->persist($exercise);

            // Content
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentExercise(
                        exercise   : $exercise,
                        locale     : $locale,
                        title      : $contentData['title'],
                        summary    : $contentData['summary'],
                        details    : $contentData['details'],
                        titlePlural: $contentData['titlePlural'] ?? null,
                    );

                    $manager->persist($content);
                    $exercise->addContent($content);
                }
            }
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            ExerciseCategoryFixtures::class,
            EquipmentFixtures::class,
            MuscleFixtures::class,
            MuscleSegmentFixtures::class,
        ];
    }
}
