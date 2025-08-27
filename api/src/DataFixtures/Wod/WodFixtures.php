<?php

namespace App\DataFixtures\Wod;

use App\DataFixtures\Exercise\ExerciseFixtures;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Entity\WodVersionVariantExercise;
use App\Domain\Wod\Enum\Gender;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFiles = [
            __DIR__ . '/../../../default_data/wod/wods_test.json',
        ];

        foreach ($jsonFiles as $jsonFile) {
            if (!file_exists($jsonFile)) {
                throw new Exception("Le fichier JSON n'existe pas : " . $jsonFile);
            }

            $jsonData = file_get_contents($jsonFile);
            $data = json_decode($jsonData, true);

            if ($data === null) {
                throw new Exception("Format JSON invalide dans le fichier : " . $jsonFile);
            }

            foreach ($data as $item) {
                $wod = new Wod();
                $wod->setTitle($item['title']);
                $wod->setDescription($item['description']);

                $type = $manager->getRepository(WodType::class)->findOneBy(['slug' => $item['type']]);
                if (!$type) {
                    throw new Exception("WodType non trouvé : " . $item['type']);
                }
                $wod->setWodType($type);

                $category = $manager->getRepository(WodCategory::class)->findOneBy(['slug' => $item['category']]);
                if (!$category) {
                    throw new Exception("WodCategory non trouvé : " . $item['category']);
                }
                $wod->setWodCategory($category);

                foreach ($item['versions'] as $wodVersionData) {
                    $wodVersion = new WodVersion();

                    $wodVersionType = $manager->getRepository(WodVersionType::class)->findOneBy(['slug' => $wodVersionData['type']]);
                    if (!$wodVersionType) {
                        throw new \Exception("WodVersionType non trouvé : " . $wodVersionData['type']);
                    }

                    $wodVersion->setWodVersionType($wodVersionType);

                    foreach ($wodVersionData['variants'] as $variantData) {
                        $variant = new WodVersionVariant();

                        $genderEnum = Gender::tryFrom($variantData['gender']);
                        if (!$genderEnum) {
                            throw new \Exception("Genre inconnu : " . $variantData['gender']);
                        }

                        $variant->setGender($genderEnum);
                        $variant->setRounds($variantData['rounds'] ?? null);
                        $variant->setTimeCap($variantData['time_cap'] ?? null);
                        $variant->setDescription($variantData['description']);

                        foreach ($variantData['exercises'] as $index => $exerciseData) {
                            $exercise = $manager->getRepository(Exercise::class)->findOneBy(['slug' => $exerciseData['exercice']]);
                            if (!$exercise) {
                                throw new \Exception("Exercice non trouvé : " . $exerciseData['exercice']);
                            }

                            $variantExercise = new WodVersionVariantExercise();
                            $variantExercise->setPosition((int) $index + 1);
                            $variantExercise->setReps($exerciseData['reps'] ? (int) $exerciseData['reps'] : null);
                            $variantExercise->setWeight($exerciseData['weight'] ? (int) $exerciseData['weight'] : null);
                            $variantExercise->setExercise($exercise);
                            $variantExercise->setWodVersionVariant($variant);

                            $variant->addWodVersionVariantExercise($variantExercise);
                        }

                        $variant->setWodVersion($wodVersion);
                        $wodVersion->addWodVersionVariant($variant);
                    }

                    $wod->addWodVersion($wodVersion);
                }

                $manager->persist($wod);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WodTypeFixtures::class,
            WodVersionTypeFixtures::class,
            ExerciseFixtures::class,
        ];
    }
}
