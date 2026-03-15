import { ExerciseSummary } from "../exercises/types"

// ---------------------------------------------------------------------------
// Core types
// ---------------------------------------------------------------------------

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
  allowedMetrics: object
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
  gender: "male" | "female" | "mixed"
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
  type: MetricType
  value: number
}

export enum MetricType {
  REPETITIONS = "repetitions",
  TIME        = "time",
  DISTANCE    = "distance",
  CALORIES    = "calories",
}

// ---------------------------------------------------------------------------
// Payloads
// ---------------------------------------------------------------------------

export type CreateWodPayload = {
  slug: string
  title: string
  summary: string
  details: string
}

export type UpdateWodPayload = CreateWodPayload
