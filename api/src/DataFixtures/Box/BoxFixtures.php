<?php

namespace App\DataFixtures\Box;

use App\Domain\Box\Entity\Box;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class BoxFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/box/boxs.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        foreach ($data as $item) {

            if (empty($item['name'])) {
                throw new Exception('Box invalide : le champ "name" est obligatoire');
            }

            $existingBox = $manager->getRepository(Box::class)
                ->findOneBy(['name' => $item['name']]);

            if ($existingBox) {
                continue; // saute la box existante
            }

            $box = new Box(
                name: $item['name']
            );
            $box->setAddress($item['address'] ?? null);
            $box->setZipCode($item['zipCode'] ?? null);
            $box->setCity($item['city'] ?? null);
            $box->setCountry($item['country'] ?? null);
            $box->setLat($item['lat'] ?? null);
            $box->setLng($item['lng'] ?? null);
            $box->setPhone($item['phone'] ?? null);
            $box->setEmail($item['email'] ?? null);
            $box->setWebsite($item['website'] ?? null);

            if (!empty($item['social'])) {
                $box->setFacebook($item['social']['facebook'] ?? null);
                $box->setInstagram($item['social']['instagram'] ?? null);
            }

            $manager->persist($box);
        }

        $manager->flush();
    }
}
