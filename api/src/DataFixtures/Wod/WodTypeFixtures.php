<?php

namespace App\DataFixtures\Wod;

use App\Domain\Content\Entity\ContentWodType;
use App\Domain\Wod\Entity\WodType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodTypeFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/types.json';

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
            // WOD TYPE
            // ---------------------------------------------------------------------------------------------------------
            $wodType = new WodType();
            $wodType->setSlug($item['slug']);
            $wodType->setAllowedMetrics($item['allowedMetrics']);

            // Contents
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentWodType(
                        wodType: $wodType,
                        locale : $locale,
                        title  : $contentData['title'],
                        summary: $contentData['summary'],
                        details: $contentData['details'],
                    );

                    $manager->persist($content);
                    $wodType->addContent($content);
                }
            }

            $manager->persist($wodType);
        }

        $manager->flush();
    }
}
