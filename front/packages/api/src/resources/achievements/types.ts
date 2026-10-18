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
  level: Level
  unlockRatio: number
  rarity: Rarity
  description: string
}

export const LEVEL = {
  Default: "DEFAULT",
  Bronze: "BRONZE",
  Silver: "SILVER",
  Gold: "GOLD",
  Platinum: "PLATINUM",
} as const
export type Level = typeof LEVEL[keyof typeof LEVEL]

export const RARITY = {
  Common: "COMMON",
  Uncommon: "UNCOMMON",
  Rare: "RARE",
  VeryRare: "VERY_RARE",
  UltraRare: "ULTRA_RARE",
} as const
export type Rarity = typeof RARITY[keyof typeof RARITY]

export type CreateAchievementPayload = {
  slug: string
  title: string
  summary: string
  details: string
}

export type UpdateAchievementPayload = CreateAchievementPayload
