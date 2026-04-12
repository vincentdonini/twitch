import { useTranslations } from "next-intl"

export function formatSecondsToDynamic(seconds: number): string {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  return [h > 0 ? `${h}h` : null, m > 0 ? `${m}min` : null, s > 0 ? `${s}s` : null]
    .filter(Boolean)
    .join(" ") || `${seconds}s`
}

export function formatExerciseMetric(
  type: string,
  value: number,
  t: ReturnType<typeof useTranslations<"common.wods">>,
): string {
  switch (type) {
    case "calories": return t("metric_calories", { value })
    case "distance": return t("metric_distance", { value })
    case "time": return t("metric_time", { value })
    case "weight": return t("metric_weight", { value })
    case "height": return t("metric_height", { value })
    default: return `${value}`
  }
}
