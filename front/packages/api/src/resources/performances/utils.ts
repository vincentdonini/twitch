import { METRIC, type Metric, type ScoreEntry, TREND, Trend } from "./types"

export function scoreNum(score: ScoreEntry): number {
  return score.time ?? score.repetitions ?? score.weight ?? 0
}

export function formatScore(score: ScoreEntry): string {
  if (score.time != null) {
    const m = Math.floor(score.time / 60)
    const s = score.time % 60
    return `${m}:${String(s).padStart(2, "0")}`
  }

  if (score.repetitions != null) {
    return `${score.repetitions} rds`
  }

  if (score.weight != null) {
    return `${score.weight}kg`
  }

  return "—"
}

export function isPR(
  score: ScoreEntry,
  all: ScoreEntry[],
  metrics: Metric[]
): boolean {
  const current = resolveScoreValue(score, metrics)

  if (!current) return false

  return all.every((entry) => {
    const candidate = resolveScoreValue(entry, metrics)

    if (!candidate) return true

    const currentPriority = metrics.indexOf(current.metric)
    const candidatePriority = metrics.indexOf(candidate.metric)

    // Une métrique prioritaire bat toujours une moins prioritaire
    if (candidatePriority < currentPriority) return false
    if (candidatePriority > currentPriority) return true

    // Même métrique → comparaison selon le type
    if (current.metric === METRIC.Time) {
      return current.value <= candidate.value
    }

    return current.value >= candidate.value
  })
}

export function resolveMetricFromType(type: string): Metric {
  const t = type.toLowerCase()

  if (t.includes("time")) return METRIC.Time

  if (
    t.includes("weight") ||
    t.includes("max") ||
    t.includes("rm")
  ) {
    return METRIC.Weight
  }

  return METRIC.Repetitions
}

export function resolveScoreValue(
  score: ScoreEntry,
  metrics: Metric[]
): { value: number; metric: Metric } | null {
  for (const metric of metrics) {
    if (metric === METRIC.Time && score.time != null) {
      return { value: score.time, metric }
    }

    if (metric === METRIC.Repetitions && score.repetitions != null) {
      return { value: score.repetitions, metric }
    }

    if (metric === METRIC.Weight && score.weight != null) {
      return { value: score.weight, metric }
    }
  }

  return null
}

export function getPRScore(
  scores: ScoreEntry[],
  metrics: Metric[]
): ScoreEntry {
  return scores.reduce((best, current) => {
    const bestResolved = resolveScoreValue(best, metrics)
    const currentResolved = resolveScoreValue(current, metrics)

    if (!bestResolved) return current
    if (!currentResolved) return best

    // Si la métrique prioritaire n'est pas la même
    const bestPriority = metrics.indexOf(bestResolved.metric)
    const currentPriority = metrics.indexOf(currentResolved.metric)

    if (currentPriority < bestPriority) return current
    if (currentPriority > bestPriority) return best

    // Même métrique → comparaison normale
    if (currentResolved.metric === METRIC.Time) {
      return currentResolved.value < bestResolved.value ? current : best
    }

    return currentResolved.value > bestResolved.value ? current : best
  })
}

export function getTrend(
  scores: ScoreEntry[],
  metric: Metric
): Trend {
  if (scores.length < 2) return TREND.None

  const latest = scoreNum(scores[0] as ScoreEntry)
  const prev = scoreNum(scores[1] as ScoreEntry)

  if (latest === prev) return TREND.Flat

  if (metric === METRIC.Time) {
    return latest < prev ? TREND.Up : TREND.Down
  }

  return latest > prev ? TREND.Up : TREND.Down
}