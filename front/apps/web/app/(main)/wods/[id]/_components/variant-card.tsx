"use client"

import { useTranslations } from "next-intl"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent } from "@workspace/ui/components/card"
import type { ExerciseMetric, WodVariant } from "@workspace/api"

export function VariantCard({ variant }: { variant: WodVariant }) {
  const t = useTranslations("wods")

  const genderKey = `gender_${variant.gender}` as "gender_male" | "gender_female" | "gender_mixed"

  return (
    <Card>
      <CardContent className="space-y-4 p-5">
        {/* Variant header */}
        <div className="space-y-1">
          <div className="flex flex-wrap gap-1.5">
            <Badge variant="outline" className="text-xs">{variant.division.title}</Badge>
            <Badge variant="secondary" className="text-xs">{t(genderKey)}</Badge>
            {variant.ageRange && (
              <Badge variant="secondary" className="text-xs">{variant.ageRange.title}</Badge>
            )}
          </div>
          <div className="flex gap-4 text-sm text-muted-foreground pt-1">
            {variant.rounds != null && (
              <span>{t("rounds", { count: variant.rounds })}</span>
            )}
            {variant.timeCap != null && (
              <span>{t("time_cap", { value: variant.timeCap })}</span>
            )}
          </div>
        </div>

        {/* Exercises */}
        {variant.exercises.length > 0 && (
          <ol className="space-y-2">
            {variant.exercises
              .sort((a, b) => a.position - b.position)
              .map(we => (
                <li key={we.id} className="flex items-start gap-3 text-sm">
                  <span className="text-muted-foreground font-mono w-4 shrink-0 pt-0.5">
                    {we.position}.
                  </span>
                  <div className="flex flex-wrap items-baseline gap-x-2">
                    <span className="font-medium">{we.exercise.title}</span>
                    {we.metrics.map(m => (
                      <span key={m.id} className="text-muted-foreground text-xs">
                        {formatMetric(m, t)}
                      </span>
                    ))}
                  </div>
                </li>
              ))
            }
          </ol>
        )}
      </CardContent>
    </Card>
  )
}

function formatMetric(
  metric: ExerciseMetric,
  t: ReturnType<typeof useTranslations<"wods">>,
): string {
  const key = `metric_${metric.type}` as
    | "metric_repetitions"
    | "metric_time"
    | "metric_distance"
    | "metric_calories"
  return t(key, { value: metric.value })
}
