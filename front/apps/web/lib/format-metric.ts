import { type ExerciseMetric } from "@workspace/api"
import type { useTranslations } from "next-intl"

export function formatMetric(
  metric: ExerciseMetric,
  t: ReturnType<typeof useTranslations<"wods">>,
  options?: { onlyValue?: boolean },
): string {
  if (options?.onlyValue) return String(metric.value)

  const key = `metric_${metric.type}` as
    | "metric_repetitions"
    | "metric_weight"
    | "metric_time"
    | "metric_distance"
    | "metric_calories"

  return t(key, { value: metric.value })
}
