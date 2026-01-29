<?php

namespace App\DataFixtures\Benchmark;

use App\DataFixtures\User\UserFixtures;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Enum\TypeEnum;
use App\Domain\User\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class BenchmarkScoreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/benchmark/benchmark_scores.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("Benchmark scores JSON file not found: $jsonFile");
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in $jsonFile");
        }

        foreach ($data as $userData) {
            if (empty($userData['user']) || empty($userData['scores'])) {
                throw new Exception("Invalid score entry (user/scores missing)");
            }

            /** @var User|null $user */
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $userData['user']]);

            if (!$user) {
                throw new Exception("User not found: {$userData['user']}");
            }

            foreach ($userData['scores'] as $scoreData) {
                if (empty($scoreData['benchmark'])) {
                    throw new Exception("Benchmark missing for score");
                }

                /** @var Benchmark|null $benchmark */
                $benchmark = $manager->getRepository(Benchmark::class)->findOneBy(['name' => $scoreData['benchmark']]);

                if (!$benchmark) {
                    throw new Exception("Benchmark not found: {$scoreData['benchmark']}");
                }

                $performedAt = !empty($scoreData['performedAt'])
                    ? new DateTimeImmutable($scoreData['performedAt'])
                    : new DateTimeImmutable();

                $private = $scoreData['private'] ?? false;

                $score = new BenchmarkScore(
                    user       : $user,
                    benchmark  : $benchmark,
                    performedAt: $performedAt,
                    private    : $private
                );

                switch ($benchmark->getType()) {
                    case TypeEnum::WEIGHT:
                        $score->setWeight($scoreData['weight'] ?? null);
                        break;

                    case TypeEnum::TIME:
                        $score->setTime($scoreData['time'] ?? null);
                        break;

                    case TypeEnum::REPETITIONS:
                        $score->setRepetitions($scoreData['repetitions'] ?? null);
                        break;

                    default:
                        throw new Exception("Unsupported benchmark type: {$benchmark->getType()->value}");
                }

                if (!empty($scoreData['notes'])) {
                    $score->setNotes($scoreData['notes']);
                }

                $score->assertScoreIsValid();

                $manager->persist($score);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BenchmarkFixtures::class,
        ];
    }
}
