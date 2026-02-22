<?php

namespace App\DataFixtures\Wod;

use App\Domain\Content\Entity\ContentWodDivision;
use App\Domain\Wod\Entity\WodDivision;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodDivisionFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/divisions.json';

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
            // WOD DIVISION
            // ---------------------------------------------------------------------------------------------------------
            $wodDivision = new WodDivision(
                slug: $item['slug'],
            );

            // Contents
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentWodDivision(
                        wodDivision: $wodDivision,
                        locale     : $locale,
                        title      : $contentData['title'],
                        summary    : $contentData['summary'],
                        details    : $contentData['details'],
                    );

                    $manager->persist($content);
                    $wodDivision->addContent($content);
                }
            }

            $manager->persist($wodDivision);
        }

        $manager->flush();
    }
}
