export const METRIC = {
  Time: "time",
  Weight: "weight",
  Repetitions: "repetitions",
} as const
export type Metric = typeof METRIC[keyof typeof METRIC]

export type ScoreEntry = {
  id: string
  time: number | null
  repetitions: number | null
  weight: number | null
  performedAt: string
  notes: string | null
}

export const TREND = {
  Up: "up",
  Down: "down",
  Flat: "flat",
  None: null,
} as const

export type Trend = typeof TREND[keyof typeof TREND]