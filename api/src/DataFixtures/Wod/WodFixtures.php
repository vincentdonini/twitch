<?php

namespace App\DataFixtures\Wod;

use App\DataFixtures\Movement\MovementFixtures;
use App\Domain\Movement\Entity\Movement;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Entity\WodVersionVariantMovement;
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
            __DIR__ . '/../../../default_data/wod/wods_girls.json',
            __DIR__ . '/../../../default_data/wod/wods_heroes.json',
            __DIR__ . '/../../../default_data/wod/wods_open.json',
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
                $wod->setDescriptions($item['descriptions']);

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
                        throw new Exception("WodVersionType non trouvé : " . $wodVersionData['type']);
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
                        $variant->setDescriptions($variantData['descriptions']);

                        foreach ($variantData['movements'] as $index => $movementData) {
                            $movement = $manager->getRepository(Movement::class)->findOneBy(['slug' => $movementData['movement']]);
                            if (!$movement) {
                                throw new Exception("Movement non trouvé : " . $movementData['movement']);
                            }

                            $variantMovement = new WodVersionVariantMovement();
                            $variantMovement->setPosition((int) $index + 1);
                            $variantMovement->setReps($movementData['reps'] ? (int) $movementData['reps'] : null);
                            $variantMovement->setWeight($movementData['weight'] ? (int) $movementData['weight'] : null);
                            $variantMovement->setMovement($movement);
                            $variantMovement->setWodVersionVariant($variant);

                            $variant->addWodVersionVariantMovement($variantMovement);
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
            MovementFixtures::class,
            WodTypeFixtures::class,
            WodVersionTypeFixtures::class,
        ];
    }
}
