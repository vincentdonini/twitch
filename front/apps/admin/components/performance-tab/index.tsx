"use client"

import { ScoreChart } from "@/components/performance-tab/score-chart"
import { ScorePr } from "@/components/performance-tab/score-pr"
import { ScoreTimeline } from "@/components/performance-tab/score-time-line"
import { getPRScore, type Metric, type ScoreEntry } from "@workspace/api"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { TrendingUp } from "lucide-react"
import { useTranslations } from "next-intl"

interface PerformanceTabProps {
  scores: ScoreEntry[] | null
  isLoading: boolean
  error: string | null
  metrics: Metric[]
  onScoreLogged: () => void
  renderLogDrawer: (onSuccess: () => void, fullWidth?: boolean) => React.ReactNode
}

export function PerformanceTab(
  {
    scores,
    isLoading,
    error,
    metrics,
    onScoreLogged,
    renderLogDrawer,
  }: PerformanceTabProps,
) {
  const tc = useTranslations("common.performance")

  // Loading
  if (isLoading) {
    return (
      <div className="grid grid-cols-1 gap-6">
        <Card>
          <div className="grid grid-cols-1 lg:gap-0 lg:grid-cols-5 divide-y lg:divide-y-0 lg:divide-x">
            <div className="lg:col-span-3 flex flex-col">
              <CardContent className="px-6">
                <Skeleton className="h-[220px] w-full" />
              </CardContent>
            </div>
            <div className="flex flex-col col-span-2">
              <CardContent className="px-6">
                <Skeleton className="h-[220px] w-full" />
              </CardContent>
            </div>
          </div>
        </Card>

        <div className="space-y-3">
          <Skeleton className="h-8 w-[200px] mb-4" />
          <div className="ml-4">
            {Array.from({ length: 4 }).map((_, i) => {
              const isLast = i === 3

              return (
                <div key={i} className="relative flex items-start pb-6">

                  {/* Vertical line */}
                  {!isLast && (
                    <div className="absolute top-[2.75rem] h-[calc(100%-2.75rem)] w-px bg-border/70" />
                  )}

                  {/* Dot / Icon */}
                  <div className="absolute ml-[-14px] py-2">
                    <Skeleton className="size-7 rounded-lg" />
                  </div>

                  {/* Content */}
                  <div className="pl-10 flex-1 min-w-0">

                    {/* Date */}
                    <Skeleton className="h-3 w-32 mb-2" />

                    {/* Score + badge */}
                    <div className="flex items-center justify-between gap-3">
                      <Skeleton className="h-6 w-20" />
                      <Skeleton className="h-4 w-10 rounded-full" />
                    </div>

                    {/* Notes */}
                    <Skeleton className="h-3 w-full mt-2" />
                    <Skeleton className="h-3 w-2/3 mt-1" />
                  </div>
                </div>
              )
            })}
          </div>
        </div>
      </div>
    )
  }

  // Error
  if (error) {
    return (
      <Card>
        <CardContent className="p-6 text-sm text-destructive">
          {tc("error")}
        </CardContent>
      </Card>
    )
  }

  // Empty
  if (!scores || scores.length === 0) {
    return (
      <Card>
        <CardContent className="p-8 flex flex-col items-center gap-4 text-center">
          <TrendingUp className="size-10 text-muted-foreground opacity-40" />
          <div>
            <p className="font-semibold">
              {tc("no_scores_yet")}
            </p>
            <p className="text-sm text-muted-foreground mt-1">
              {tc("first_attempt")}
            </p>
          </div>
          {renderLogDrawer(onScoreLogged)}
        </CardContent>
      </Card>
    )
  }

  return (
    <div className="grid grid-cols-1 gap-6">
      <Card>
        <div className="grid grid-cols-1 lg:gap-0 lg:grid-cols-5 divide-y lg:divide-y-0 lg:divide-x">
          <div className="lg:col-span-3 flex flex-col pb-4 lg:pb-0">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 mb-4">
                <TrendingUp className="size-4 text-muted-foreground" />
                {tc("score_progression")}
              </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-4 flex-1">
              <div className="w-full h-[180px] lg:h-full">
                <ScoreChart scores={scores} metrics={metrics}/>
              </div>
            </CardContent>
          </div>
          <div className="flex flex-col col-span-2 pt-4 lg:pt-0">
            <ScorePr
              scores={scores}
              metrics={metrics}
              onScoreLogged={onScoreLogged}
              renderLogDrawer={renderLogDrawer}
            />
          </div>
        </div>
      </Card>

      <div className="space-y-3">
        <h2 className="text-xl font-semibold">
          {tc("attempt_history")}
        </h2>
        <ScoreTimeline scores={scores} metrics={metrics} />
      </div>
    </div>
  )
}
