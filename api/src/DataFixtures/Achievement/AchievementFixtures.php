<?php

namespace App\DataFixtures\Achievement;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Entity\AchievementGroup;
use App\Domain\Achievement\Entity\Achievement;
use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\Content\Entity\ContentAchievement;
use App\Domain\Content\Entity\ContentAchievementCategory;
use App\Domain\Content\Entity\ContentAchievementGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class AchievementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jsonFile = __DIR__ . '/../../../default_data/achievement/achievements.json';

        if (!file_exists($jsonFile)) {
            throw new Exception("The JSON file does not exist: " . $jsonFile);
        }

        $jsonData = file_get_contents($jsonFile);
        $data     = json_decode($jsonData, true);

        if ($data === null || !isset($data['categories'])) {
            throw new Exception("Invalid JSON format in file: " . $jsonFile);
        }

        $categoryPosition = 1;

        // -------------------------------------------------------------------------------------------------------------
        // CATEGORIES
        // -------------------------------------------------------------------------------------------------------------
        foreach ($data['categories'] as $categoryData) {

            $achievementCategory = new AchievementCategory(
                code    : $categoryData['code'],
                position: $categoryData['position'] ?? $categoryPosition
            );

            //  CONTENTS
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($categoryData['contents']) && is_array($categoryData['contents'])) {
                foreach ($categoryData['contents'] as $locale => $contentData) {
                    $content = new ContentAchievementCategory(
                        achievementCategory: $achievementCategory,
                        locale             : $locale,
                        title              : $contentData['title'],
                        description        : $contentData['description'],
                    );

                    $manager->persist($content);
                    $achievementCategory->addContent($content);
                }
            }
            $categoryPosition++;

            // ---------------------------------------------------------------------------------------------------------
            // GROUPS
            // ---------------------------------------------------------------------------------------------------------
            $groupPosition = 1;
            if (!empty($categoryData['groups']) && is_array($categoryData['groups'])) {
                foreach ($categoryData['groups'] as $groupData) {

                    $achievementGroup = new AchievementGroup(
                        code               : $groupData['code'],
                        position           : $groupData['position'] ?? $groupPosition,
                        achievementCategory: $achievementCategory
                    );

                    //  CONTENTS
                    // -------------------------------------------------------------------------------------------------
                    if (!empty($groupData['contents']) && is_array($groupData['contents'])) {
                        foreach ($groupData['contents'] as $locale => $contentData) {
                            $content = new ContentAchievementGroup(
                                achievementGroup: $achievementGroup,
                                locale          : $locale,
                                title           : $contentData['title'],
                                description     : $contentData['description'],
                            );

                            $manager->persist($content);
                            $achievementGroup->addContent($content);
                        }
                    }
                    $groupPosition++;

                    $achievementCategory->addAchievementGroup($achievementGroup);

                    // -------------------------------------------------------------------------------------------------
                    // ACHIEVEMENTS
                    // -------------------------------------------------------------------------------------------------
                    $achievementPosition = 1;
                    if (!empty($groupData['achievements']) && is_array($groupData['achievements'])) {
                        foreach ($groupData['achievements'] as $achievementData) {

                            $source = AchievementSourceEnum::tryFrom($achievementData['source']);
                            if (!$source) {
                                throw new Exception("Unknown source : " . $achievementData['source']);
                            }

                            $achievement = new Achievement(
                                code            : $achievementData['code'],
                                position        : $achievementData['position'] ?? $achievementPosition,
                                source          : $source,
                                achievementGroup: $achievementGroup,
                            );

                            //  CONTENTS
                            // -----------------------------------------------------------------------------------------
                            if (!empty($achievementData['contents']) && is_array($achievementData['contents'])) {
                                foreach ($achievementData['contents'] as $locale => $contentData) {
                                    $content = new ContentAchievement(
                                        achievement: $achievement,
                                        locale     : $locale,
                                        title      : $contentData['title'],
                                        description: $contentData['description'],
                                    );

                                    $manager->persist($content);
                                    $achievement->addContent($content);
                                }
                            }

                            $achievementPosition++;

                            $achievementGroup->addAchievement($achievement);

                            // -----------------------------------------------------------------------------------------
                            // LEVELS
                            // -----------------------------------------------------------------------------------------
                            if (!empty($achievementData['levels']) && is_array($achievementData['levels'])) {
                                foreach ($achievementData['levels'] as $levelData) {

                                    $level = AchievementLevelEnum::tryFrom($levelData['level']);
                                    if (!$level) {
                                        throw new Exception("Unknown level : " . $levelData['level']);
                                    }

                                    $achievementLevel = new AchievementLevel(
                                        level      : $level,
                                        achievement: $achievement,
                                    );

                                    $achievement->addAchievementLevel($achievementLevel);
                                    $manager->persist($achievementLevel);
                                }
                            }

                            $manager->persist($achievement);
                        }
                    }

                    $manager->persist($achievementGroup);
                }
            }

            $manager->persist($achievementCategory);
        }

        $manager->flush();
    }
}
