export type Achievement = {
  id: string
  code: string
  position: number
  title: string
  description: string
  category: AchievementCategory
  group: AchievementGroup
  levels: AchievementLevel[]
}

export type AchievementCategory = {
  id: string
  code: string
  position: number
  title: string
  description: string
}

export type AchievementGroup = {
  id: string
  code: string
  position: number
  title: string
  description: string
}

export type AchievementLevel = {
  id: string
  level: AchievementLevelEnum
  unlockRatio: number
  rarity: AchievementRarityEnum
  description: string
}

export const ACHIEVEMENT_LEVEL = {
  Default: "DEFAULT",
  Bronze: "BRONZE",
  Silver: "SILVER",
  Gold: "GOLD",
  Platinum: "PLATINUM",
} as const
export type AchievementLevelEnum = typeof ACHIEVEMENT_LEVEL[keyof typeof ACHIEVEMENT_LEVEL]

export const AchievementRarity = {
  Common: "COMMON",
  Uncommon: "UNCOMMON",
  Rare: "RARE",
  VeryRare: "VERY_RARE",
  UltraRare: "ULTRA_RARE",
} as const
export type AchievementRarityEnum = typeof AchievementRarity[keyof typeof AchievementRarity]

export type CreateAchievementPayload = {
  slug: string
  title: string
  summary: string
  details: string
}

export type UpdateAchievementPayload = CreateAchievementPayload
