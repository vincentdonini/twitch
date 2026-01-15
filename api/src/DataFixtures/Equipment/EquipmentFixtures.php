<?php

namespace App\DataFixtures\Equipment;

use App\Domain\Content\Entity\ContentEquipment;
use App\Domain\Equipment\Entity\Equipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class EquipmentFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/equipments.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        usort($data, function ($a, $b) {
            return strcmp($a['slug'], $b['slug']);
        });

        foreach ($data as $item) {
            $equipment = new Equipment();
            $equipment->setSlug($item['slug']);

            $manager->persist($equipment);

            // Gestion des contenus
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($item['contents']) && is_array($item['contents'])) {
                foreach ($item['contents'] as $locale => $contentData) {
                    $content = new ContentEquipment(
                        equipment: $equipment,
                        locale   : $locale,
                        title    : $contentData['title'],
                        summary  : $contentData['summary'],
                        details  : $contentData['details'],
                    );

                    $manager->persist($content);
                    $equipment->addContent($content); // pour la collection côté Equipment
                }
            }
        }

        $manager->flush();
    }
}
