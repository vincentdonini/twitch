<?php

namespace App\DataFixtures\Wod;

use App\Domain\Content\Entity\ContentWodCategory;
use App\Domain\Wod\Entity\WodCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class WodCategoryFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/wod/categories.json';

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
            // WOD CATEGORY
            // ---------------------------------------------------------------------------------------------------------
            $wodCategory = new WodCategory(
                slug: $item['slug'],
            );

            // Contents
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentWodCategory(
                        wodCategory: $wodCategory,
                        locale     : $locale,
                        title      : $contentData['title'],
                        summary    : $contentData['summary'],
                        details    : $contentData['details'],
                    );

                    $manager->persist($content);
                    $wodCategory->addContent($content);
                }
            }

            $manager->persist($wodCategory);
        }

        $manager->flush();
    }
}
