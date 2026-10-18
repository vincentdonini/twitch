import type { ExerciseSummary } from "../exercises/exercises/types";

export type Benchmark = {
    id: string;
    slug: string;
    name: string;
    type: Type;
    category: string;
    title: string;
    summary: string;
    exercise: ExerciseSummary;
};

export type BenchmarkDetail = Benchmark & {
    details: string | null
    rules: string | null
    tips: string | null
}

export const CATEGORY = {
  Strength: "strength",
  Gymnastics: "gymnastics",
  Cardio: "cardio",
} as const

export type Category = typeof CATEGORY[keyof typeof CATEGORY]

export const TYPE = {
  Weight: "weight",
  Time: "time",
  Repetitions: "repetitions",
} as const

export type Type = typeof TYPE[keyof typeof TYPE]

export type CreateBenchmarkPayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateBenchmarkPayload = CreateBenchmarkPayload;

export type BenchmarkScore = {
  id: string
  benchmark: { id: string; name: string }
  time: number | null
  repetitions: number | null
  weight: number | null
  performedAt: string
  notes: string | null
  private: boolean
}

export type CreateBenchmarkScorePayload = {
  benchmarkId: string
  performed_at: string
  time?: number
  repetitions?: number
  weight?: number
  notes?: string
  private?: boolean
}
