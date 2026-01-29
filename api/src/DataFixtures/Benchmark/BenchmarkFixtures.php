<?php

namespace App\DataFixtures\Benchmark;

use App\DataFixtures\Exercise\ExerciseFixtures;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Enum\TypeEnum;
use App\Domain\Content\Entity\ContentBenchmark;
use App\Domain\Exercise\Entity\Exercise;
use App\Shared\Utils\StringHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class BenchmarkFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/benchmark/benchmarks.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {

            if (empty($item['name']) || empty($item['type']) || empty($item['exercise'])) {
                throw new Exception('Invalid benchmark (missing name/type/exercise)');
            }

            $exercise = $manager
                ->getRepository(Exercise::class)
                ->findOneBy(['slug' => $item['exercise']]);

            if (!$exercise) {
                throw new Exception(
                    sprintf('Exercise non trouvé pour benchmark "%s" : %s', $item['name'], $item['exercise'])
                );
            }

            $type = TypeEnum::tryFrom($item['type']);
            if (!$type) {
                throw new Exception("Unknown type : " . $item['type']);
            }

            $benchmark = new Benchmark(
                exercise: $exercise,
                slug    : StringHelper::slugify($item['name']),
                name    : $item['name'],
                type    : $type
            );
            $benchmark->setValue($item['value'] ?? null);
            $benchmark->setExercise($exercise);

            // ---------------------------------------------------------------------------------------------------------
            //  CONTENTS
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentBenchmark(
                        benchmark: $benchmark,
                        locale   : $locale,
                        title    : $contentData['title'],
                        summary  : $contentData['summary'],
                        details  : $contentData['details'] ?? null,
                        rules    : $contentData['rules'] ?? null,
                        tips     : $contentData['tips'] ?? null,
                    );

                    $manager->persist($content);
                    $benchmark->addContent($content);
                }
            }

            $manager->persist($benchmark);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExerciseFixtures::class,
        ];
    }
}
