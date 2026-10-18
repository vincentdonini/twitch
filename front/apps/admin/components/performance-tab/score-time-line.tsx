"use client"

import { cn } from "@/lib/utils"
import { formatScore, isPR, type Metric, type ScoreEntry } from "@workspace/api"
import { formatDate } from "@workspace/ui/lib/date"
import { Activity, Trophy } from "lucide-react"
import { useLocale } from "next-intl"

interface ScoreTimelineProps {
  scores: ScoreEntry[]
  metrics: Metric[]
}

export function ScoreTimeline(
  {
    scores,
    metrics,
  }: ScoreTimelineProps,
) {
  const locale = useLocale()

  return (
    <div className="ml-4">
      {scores.map((entry, i) => {
        const pr = isPR(entry, scores, metrics)
        const isLast = i === scores.length - 1

        return (
          <div key={entry.id ?? i} className="relative flex items-start pb-6">
            {/* Vertical line */}
            {!isLast && (
              <div className="absolute top-[2.75rem] h-[calc(100%-2.75rem)] w-px bg-border/70" />
            )}

            {/* ICON */}
            <div className="absolute ml-[-14px] py-2">
              <div className={cn(
                "flex size-7 shrink-0 items-center justify-center rounded-lg",
                pr ? "bg-yellow-500" : "bg-muted",
              )}>
                {pr
                  ? <Trophy className="size-4 text-white" />
                  : <Activity className="size-3 text-muted-foreground/60" />
                }
              </div>
            </div>

            {/* Content */}
            <div className="pl-10 flex-1 min-w-0">

              <div className="flex items-center justify-between gap-3">
                <div className="mt-2">
                  <span className="font-black text-xl tabular-nums leading-none">
                    {formatScore(entry)}
                  </span>
                  <p className="mb-2 text-xs font-medium text-muted-foreground">
                    {formatDate(entry.performedAt, locale, { year: "numeric", month: "long", day: "numeric" })}
                  </p>
                </div>
                <div className="flex items-center gap-1.5 flex-wrap justify-end">
                  {pr && (
                    <span
                      className="inline-flex items-center gap-1 rounded-full bg-yellow-500/10 px-2 py-0.5 text-[11px] font-semibold text-yellow-600">
                      <Trophy className="size-3" />
                      PR
                    </span>
                  )}
                </div>
              </div>

              {/* Notes */}
              {entry.notes && (
                <blockquote
                  className="mt-2 px-3 py-2 rounded-md text-xs text-muted-foreground leading-relaxed bg-secondary">
                  {entry.notes}
                </blockquote>
              )}
            </div>

          </div>
        )
      })}
    </div>
  )
}
