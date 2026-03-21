import type { WodVariant } from "@workspace/api"
import { useTranslations } from "next-intl"
import * as React from "react"

import { formatMetric } from "@/lib/format-metric"

interface ExerciseListProps {
  variant: WodVariant
}

export function ExerciseList({ variant }: ExerciseListProps) {
  const t = useTranslations("wods")
  const sorted = [...variant.exercises].sort((a, b) => a.position - b.position)

  if (sorted.length === 0) return null

  return (
    <ol>
      {sorted.map(we => {
        const repsMetric = we.metrics.find(m => m.type === "repetitions")
        const otherMetrics = we.metrics.filter(m => m.type !== "repetitions")

        return (
          <li key={we.id} className="flex items-baseline gap-4">
            <div className="min-w-0">
              <div className="font-semibold text-xl md:text-2xl inline-block">
                {repsMetric && (
                  <React.Fragment>
                    {formatMetric(repsMetric, t, { onlyValue: true }) + " "}
                  </React.Fragment>
                )}
                <React.Fragment>
                  {we.exercise.title}
                </React.Fragment>
              </div>

              {otherMetrics.length > 0 && (
                <div className="text-sm text-muted-foreground whitespace-nowrap inline-block ml-2">
                  (
                  {otherMetrics.map(m => (
                    <React.Fragment key={m.id}>
                      {formatMetric(m, t)}
                    </React.Fragment>
                  ))}
                  )
                </div>
              )}
            </div>
          </li>
        )
      })}
    </ol>
  )
}
