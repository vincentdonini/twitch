import type { ExerciseSummary } from "../exercises/exercises/types"

export const Gender = {
  Male: "male",
  Female: "female",
  Mixed: "mixed",
} as const
export type Gender = typeof Gender[keyof typeof Gender]

export type Wod = {
  id: string
  name: string
  title: string
  summary: string
  type: WodType
  category: WodCategory
  variants: WodVariant[]
  teamSize: number | null
}

export type WodDetail = Wod & {
  details: string | null
  rules: string | null
  tips: string | null
}

export type WodType = {
  id: string
  slug: string
  title: string
  summary: string
  allowedMetrics: string[]
  wodCount: number
}

export type WodCategory = {
  id: string
  slug: string
  title: string
  summary: string
  wodCount: number
}

export type WodDivision = {
  id: string
  slug: string
  title: string
  summary: string
  wodCount: number
}

export type AgeRange = {
  id: string
  slug: string
  title: string
  minAge: number | null
  maxAge: number | null
}

export type WodVariant = {
  id: string
  division: WodDivision
  gender: Gender
  ageRange: AgeRange | null
  rounds: number | null
  timeCap: number | null
  exercises: WodExercise[]
}

export type WodExercise = {
  id: string
  position: number
  exercise: ExerciseSummary
  metrics: ExerciseMetric[]
}

export type ExerciseMetric = {
  id: string
  type: MetricTypeEnum
  value: number
}

export const MetricType = {
  REPETITIONS: "repetitions",
  TIME: "time",
  DISTANCE: "distance",
  CALORIES: "calories",
} as const
export type MetricTypeEnum = (typeof MetricType)[keyof typeof MetricType]

// ---------------------------------------------------------------------------
// Leaderboard
// ---------------------------------------------------------------------------

export type LeaderboardUser = {
  id: string
  firstName: string
  lastName: string
}

export type LeaderboardEntry = {
  id: string
  rank: number
  user: LeaderboardUser
  wod: { id: string; name: string }
  variant: {
    id: string
    division: { id: string; slug: string }
    gender: Gender
  }
  time: number | null
  repetitions: number | null
  weight: number | null
  performedAt: string
}

// ---------------------------------------------------------------------------
// Payloads
// ---------------------------------------------------------------------------

export type CreateWodPayload = {
  name: string
  typeId: string
  categoryId: string
  teamSize?: number | null
}

export type UpdateWodPayload = Partial<CreateWodPayload>

export type ExerciseMetricInput = {
  type: MetricTypeEnum
  value: number
}

export type ExerciseInput = {
  exerciseId: string
  metrics: ExerciseMetricInput[]
}

export type CreateWodVariantPayload = {
  divisionId: string
  gender?: Gender | null
  ageRangeId?: string | null
  rounds?: number | null
  timeCap?: number | null
  exercises?: ExerciseInput[]
}

export type UpdateWodVariantPayload = Partial<CreateWodVariantPayload>

export type CreateWodScorePayload = {
  wodId: string
  wodVersionId: string
  performedAt: string
  time?: number
  repetitions?: number
  weight?: number
  notes?: string
  private: boolean
}
