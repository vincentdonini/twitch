<?php

namespace App\DataFixtures\Wod;

use App\DataFixtures\User\UserFixtures;
use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodScore;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Enum\GenderEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Exception;
use DateTimeImmutable;

class WodScoreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/wodScores.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("JSON file not found: $jsonFile");
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        if (!is_array($data)) {
            throw new Exception("Invalid JSON in file: $jsonFile");
        }

        foreach ($data as $item) {

            // ---------------------------------------------------------------------------------------------------------
            // USER
            // ---------------------------------------------------------------------------------------------------------
            $user = $manager
                ->getRepository(User::class)
                ->findOneBy(['email' => $item['user']]);
            if (!$user instanceof User) {
                throw new Exception("User not found: " . $item['user']);
            }

            // ---------------------------------------------------------------------------------------------------------
            // WOD
            // ---------------------------------------------------------------------------------------------------------
            $wod = $manager
                ->getRepository(Wod::class)
                ->findOneBy(['name' => $item['wod']]);
            if (!$wod instanceof Wod) {
                throw new Exception("WOD not found: " . $item['wod']);
            }

            // ---------------------------------------------------------------------------------------------------------
            // DIVISION
            // ---------------------------------------------------------------------------------------------------------
            $division = $manager
                ->getRepository(WodDivision::class)
                ->findOneBy(['slug' => $item['variant']['division']]);
            if (!$division instanceof WodDivision) {
                throw new Exception("Division not found: " . $item['variant']['division']);
            }

            // ---------------------------------------------------------------------------------------------------------
            // AGE RANGE (nullable)
            // ---------------------------------------------------------------------------------------------------------
            $ageRange = null;
            if (!empty($item['variant']['ageRange'])) {
                $ageRange = $manager
                    ->getRepository(WodAgeRange::class)
                    ->findOneBy(['slug' => $item['variant']['ageRange']]);
                if (!$ageRange instanceof WodAgeRange) {
                    throw new Exception("AgeRange not found: " . $item['variant']['ageRange']);
                }
            }

            // ---------------------------------------------------------------------------------------------------------
            // GENDER (nullable)
            // ---------------------------------------------------------------------------------------------------------
            $gender = null;
            if (!empty($item['variant']['gender'])) {
                $gender = GenderEnum::from($item['variant']['gender']);
            }

            // ---------------------------------------------------------------------------------------------------------
            // VARIANT
            // ---------------------------------------------------------------------------------------------------------
            $variant = $manager
                ->getRepository(WodVariant::class)
                ->findOneBy([
                    'wod'         => $wod,
                    'wodDivision' => $division,
                    'wodAgeRange' => $ageRange,
                    'gender'      => $gender,
                ]);

            if (!$variant instanceof WodVariant) {
                throw new Exception(
                    sprintf(
                        "Variant not found for WOD '%s' (%s, %s, %s)",
                        $item['wod'],
                        $division->getSlug(),
                        $ageRange?->getSlug() ?? 'no-age',
                        $gender?->value ?? 'no-gender'
                    ));
            }

            // ---------------------------------------------------------------------------------------------------------
            // SCORE
            // ---------------------------------------------------------------------------------------------------------
            $score = new WodScore(
                user       : $user,
                wod        : $wod,
                wodVariant : $variant,
                performedAt: new DateTimeImmutable($item['performedAt']),
                private    : $item['private'] ?? false
            );

            $score->setTime($item['time'] ?? null);
            $score->setRepetitions($item['repetitions'] ?? null);
            $score->setWeight($item['weight'] ?? null);

            $manager->persist($score);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            WodFixtures::class,
        ];
    }
}
