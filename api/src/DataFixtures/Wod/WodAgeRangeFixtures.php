<?php

namespace App\DataFixtures\Wod;

use App\Domain\Content\Entity\ContentWodAgeRange;
use App\Domain\Wod\Entity\WodAgeRange;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodAgeRangeFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/ageRanges.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {

            // ---------------------------------------------------------------------------------------------------------
            // WOD AGE RANGE
            // ---------------------------------------------------------------------------------------------------------
            $wodAgeRange = new WodAgeRange(
                slug: $item['slug']
            );
            $wodAgeRange->setMinAge($item['min_age'] ?? null);
            $wodAgeRange->setMaxAge($item['max_age'] ?? null);

            // Contents
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentWodAgeRange(
                        wodAgeRange: $wodAgeRange,
                        locale     : $locale,
                        title      : $contentData['title'],
                        summary    : $contentData['summary'],
                        details    : $contentData['details'],
                    );

                    $manager->persist($content);
                    $wodAgeRange->addContent($content);
                }
            }

            $manager->persist($wodAgeRange);
        }

        $manager->flush();
    }
}
