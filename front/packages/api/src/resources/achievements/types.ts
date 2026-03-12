export type Achievement = {
    id: string;
    code: string;
    position: number;
    title: string;
    description: string;
    category: AchievementCategory;
    group: AchievementGroup;
    levels: AchievementLevel[];
};

export type AchievementCategory = {
    id: string;
    code: string;
    position: number;
    title: string;
    description: string;
};

export type AchievementGroup = {
    id: string;
    code: string;
    position: number;
    title: string;
    description: string;
};

export type AchievementLevel = {
    id: string;
    level: AchievementLevelEnum;
    unlockRatio: number;
    rarity: AchievementRarityEnum;
    description: string;
};

export enum AchievementLevelEnum {
    DEFAULT = "DEFAULT",
    BRONZE = "BRONZE",
    SILVER = "SILVER",
    GOLD = "GOLD",
    PLATINUM = "PLATINUM",
}

export enum AchievementRarityEnum {
    COMMON = "COMMON",
    UNCOMMON = "UNCOMMON",
    RARE = "RARE",
    VERY_RARE = "VERY_RARE",
    ULTRA_RARE = "ULTRA_RARE",
}

export type CreateAchievementPayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateAchievementPayload = CreateAchievementPayload;
