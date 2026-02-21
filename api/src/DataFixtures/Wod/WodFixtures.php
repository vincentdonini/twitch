<?php

namespace App\DataFixtures\Wod;

use App\DataFixtures\Exercise\ExerciseFixtures;
use App\Domain\Content\Entity\ContentWod;
use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Entity\WodVariantExercise;
use App\Domain\Wod\Entity\WodVariantExerciseMetric;
use App\Domain\Wod\Enum\GenderEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFilesClassicBenchmarks = [
            __DIR__ . '/../../../default_data/wod/wods/classic-benchmarks/wods.json',
        ];

        $jsonFilesGirls = [
//            __DIR__ . '/../../../default_data/wod/wods/the-girls/girls.json',
        ];

        $jsonFilesHeroes = [
//            __DIR__ . '/../../../default_data/wod/wods/the-heroes/heroes.json',
        ];

        $jsonFilesOpen = [
            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_25.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_24.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_23.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_22.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_21.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_20.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_19.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_18.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_17.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_16.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_15.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_14.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_13.json',
            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_12.json',
            //            __DIR__ . '/../../../default_data/wod/wods/the-opens/open_11.json',
        ];

        $jsonFiles = array_merge(
            $jsonFilesClassicBenchmarks,
            $jsonFilesGirls,
            $jsonFilesHeroes,
            $jsonFilesOpen,
        );

        foreach ($jsonFiles as $jsonFile) {
            if (!file_exists($jsonFile)) {
                throw new Exception("Le fichier JSON n'existe pas : " . $jsonFile);
            }

            $jsonData = file_get_contents($jsonFile);
            $data     = json_decode($jsonData, true);

            if ($data === null) {
                throw new Exception("Format JSON invalide dans le fichier : " . $jsonFile);
            }

            foreach ($data as $item) {

                // -----------------------------------------------------------------------------------------------------
                //  WOD
                // -----------------------------------------------------------------------------------------------------
                $wodType = $manager->getRepository(WodType::class)->findOneBy(['slug' => $item['type']]);
                if (!$wodType) {
                    throw new Exception("WodType non trouvé : " . $item['type']);
                }

                $wodCategory = $manager->getRepository(WodCategory::class)->findOneBy(['slug' => $item['category']]);
                if (!$wodCategory) {
                    throw new Exception("WodCategory non trouvé : " . $item['category']);
                }

                $wod = new Wod(
                    name       : $item['name'],
                    wodType    : $wodType,
                    wodCategory: $wodCategory,
                );
                $wod->setTeamSize($item['team_size'] ?? null);

                // -----------------------------------------------------------------------------------------------------
                //  CONTENTS
                // -----------------------------------------------------------------------------------------------------
                if (!empty($item['contents']) && is_array($item['contents'])) {
                    foreach ($item['contents'] as $locale => $contentData) {
                        $content = new ContentWod(
                            wod    : $wod,
                            locale : $locale,
                            title  : $contentData['title'],
                            summary: $contentData['summary'],
                            details: $contentData['details'] ?? null,
                            rules  : $contentData['rules'] ?? null,
                            tips   : $contentData['tips'] ?? null,
                        );

                        $manager->persist($content);
                        $wod->addContent($content);
                    }
                }

                // -----------------------------------------------------------------------------------------------------
                //  VARIANTS
                // -----------------------------------------------------------------------------------------------------
                foreach ($item['variants'] as $wodVariantData) {

                    $wodVariant = new WodVariant();
                    $wodVariant->setWod($wod);

                    // -------------------------------------------------------------------------------------------------
                    // DIVISION
                    // -------------------------------------------------------------------------------------------------
                    $wodDivision = $manager
                        ->getRepository(WodDivision::class)
                        ->findOneBy(['slug' => $wodVariantData['division']]);

                    if (!$wodDivision) {
                        throw new Exception("WodDivision non trouvé : " . $wodVariantData['division']);
                    }

                    $wodVariant->setWodDivision($wodDivision);

                    // -------------------------------------------------------------------------------------------------
                    // SCALED / RX
                    // -------------------------------------------------------------------------------------------------
                    $wodVariant->setIsScaled((bool)($wodVariantData['scaled'] ?? false));

                    // -------------------------------------------------------------------------------------------------
                    // GENDER
                    // -------------------------------------------------------------------------------------------------
                    if (!empty($wodVariantData['gender'])) {
                        $gender = GenderEnum::tryFrom($wodVariantData['gender']);
                        if (!$gender) {
                            throw new Exception("Genre inconnu : " . $wodVariantData['gender']);
                        }
                        $wodVariant->setGender($gender);
                    }

                    // -------------------------------------------------------------------------------------------------
                    // AGE RANGE
                    // -------------------------------------------------------------------------------------------------
                    if (!empty($wodVariantData['ageRange'])) {
                        $wodAgeRange = $manager
                            ->getRepository(WodAgeRange::class)
                            ->findOneBy(['slug' => $wodVariantData['ageRange']]);

                        if (!$wodAgeRange) {
                            throw new Exception("Tranche d'age non trouvée : " . $wodVariantData['ageRange']);
                        }

                        $wodVariant->setWodAgeRange($wodAgeRange);
                    }

                    $wodVariant->setRounds($wodVariantData['rounds'] ?? null);
                    $wodVariant->setTimeCap($wodVariantData['time_cap'] ?? null);

                    // ---------------------------------------------------------------------------------------------
                    // EXERCISES
                    // ---------------------------------------------------------------------------------------------
                    foreach ($wodVariantData['exercises'] as $index => $exerciseData) {

                        $exercise = $manager
                            ->getRepository(Exercise::class)
                            ->findOneBy(['slug' => $exerciseData['exercise']]);

                        if (!$exercise) {
                            throw new Exception("Exercise non trouvé : " . $exerciseData['exercise']);
                        }

                        $variantExercise = new WodVariantExercise();
                        $variantExercise->setExercise($exercise);
                        $variantExercise->setPosition((int)$index + 1);

                        $wodVariant->addWodVariantExercise($variantExercise);

                        // -----------------------------------------------------------------------------------------
                        // METRICS
                        // -----------------------------------------------------------------------------------------
                        foreach ($exerciseData['metrics'] ?? [] as $metricSlug => $metricValue) {
                            if ($metricValue === null || $metricValue === '') {
                                continue;
                            }

                            $metric = new WodVariantExerciseMetric();
                            $metric->setExercise($variantExercise);
                            $metric->setType($metricSlug);
                            $metric->setValue((float)$metricValue);

                            $variantExercise->addMetric($metric);
                        }
                    }

                    $wod->addWodVariant($wodVariant);
                }

                $manager->persist($wod);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExerciseFixtures::class,
            WodTypeFixtures::class,
            WodAgeRangeFixtures::class,
        ];
    }
}
