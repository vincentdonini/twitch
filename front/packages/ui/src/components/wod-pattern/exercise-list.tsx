"use client"

import { type WodVariant } from "@workspace/api"
import { useTranslations } from "next-intl"
import * as React from "react"
import { formatExerciseMetric } from "./helpers"

interface ExerciseListProps {
  variant: WodVariant
  t: ReturnType<typeof useTranslations<"common.wods">>
}

export function ExerciseList({ variant, t }: ExerciseListProps) {
  const sorted = [...variant.exercises].sort((a, b) => a.position - b.position)
  if (sorted.length === 0) return null

  return (
    <ol className="space-y-1 pl-0">
      {sorted.map(we => {
        const repsMetric = we.metrics.find(m => m.type === "repetitions")
        const otherMetrics = we.metrics.filter(m => m.type !== "repetitions")
        return (
          <li key={we.id} className="flex items-baseline gap-4">
            <div className="min-w-0">
              <span className="font-semibold text-xl md:text-2xl">
                {repsMetric && `${repsMetric.value} `}
                {repsMetric && Number(repsMetric.value) > 1 && we.exercise.titlePlural
                  ? we.exercise.titlePlural
                  : we.exercise.title}
              </span>
              {otherMetrics.length > 0 && (
                <span className="text-sm text-muted-foreground ml-2">
                  ({otherMetrics.map((m, i) => (
                  <React.Fragment key={m.id}>
                    {i > 0 && ", "}
                    {formatExerciseMetric(m.type, m.value, t)}
                  </React.Fragment>
                ))})
                </span>
              )}
            </div>
          </li>
        )
      })}
    </ol>
  )
}
