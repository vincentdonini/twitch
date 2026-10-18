import {
  formatScore,
  getPRScore,
  resolveMetricFromType,
  getTrend,
  type Benchmark,
  type BenchmarkScore,
  type ScoreEntry,
  type Trend,
} from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent, CardHeader } from "@workspace/ui/components/card"
import { formatDate } from "@workspace/ui/lib/date"
import { ArrowRight, Minus, TrendingDown, TrendingUp, Trophy } from "lucide-react"
import { useLocale, useTranslations } from "next-intl"
import Link from "next/link"

interface TrendBadgeProps {
  trend: Trend
}

function TrendBadge(
  {
    trend,
  }: TrendBadgeProps,
) {
  if (!trend) return null
  if (trend === "flat") return <Minus className="size-3.5 text-muted-foreground" />
  return trend === "up"
    ? <TrendingUp className="size-3.5 text-success" />
    : <TrendingDown className="size-3.5 text-error" />
}

interface BenchmarkCardProps {
  benchmark: Benchmark
  scores?: BenchmarkScore[]
}

export function BenchmarkCard(
  {
    benchmark,
    scores = [],
  }: BenchmarkCardProps,
) {
  const locale = useLocale()
  const tc = useTranslations("common")

  const metric = resolveMetricFromType(benchmark.type)
  const pr = scores.length ? getPRScore(scores as ScoreEntry[], [metric]) : null
  const trend = getTrend(scores as ScoreEntry[], metric)

  return (
    <Link href={`/benchmarks/${benchmark.id}`}>
      <Card className="h-full overflow-hidden transition-shadow hover:shadow-md cursor-pointer">
        <CardHeader>
          <div className="space-y-3">
            <div className="flex flex-wrap gap-1.5">
              <Badge color="primary" className="text-xs">
                {tc(`benchmarks.categories.${benchmark.category}`)}
              </Badge>
              <Badge className="text-xs">
                {tc(`benchmarks.types.${benchmark.type}`)}
              </Badge>
            </div>

            <div>
              <h3 className="text-xl font-bold leading-snug mb-0">
                {benchmark.title || benchmark.name}
              </h3>
              {benchmark.exercise?.title && (
                <div className="text-muted-foreground text-xs tracking-widest uppercase">
                  {benchmark.exercise.title}
                </div>
              )}
            </div>
          </div>
        </CardHeader>

        <CardContent className="flex flex-col gap-4 flex-1">
          {benchmark.summary && (
            <p className="text-sm text-muted-foreground line-clamp-2">
              {benchmark.summary}
            </p>
          )}

          <div className="flex flex-1 items-center justify-between pt-3 border-t">
            <div className="truncate">
              {pr ? (
                <>
                  <div className="flex items-center gap-1.5">
                    <Trophy className="size-3.5 text-yellow-500 shrink-0" />
                    <span className="text-sm font-semibold tabular-nums">
                    {formatScore(pr)}
                  </span>
                    <TrendBadge trend={trend} />
                  </div>
                  <p className="text-xs text-muted-foreground mt-0.5 pl-5 truncate">
                    {formatDate(pr.performedAt, locale, { year: "numeric", month: "long", day: "numeric" })}
                  </p>
                </>
              ) : (
                <p className="text-xs text-muted-foreground truncate">
                  {tc("performance.no_scores_yet")}
                </p>
              )}
            </div>
            <span className="text-primary inline-flex items-center gap-1 text-sm hover:underline">
              {tc("button.view")} <ArrowRight className="size-3" />
            </span>
          </div>
        </CardContent>
      </Card>
    </Link>
  )
}
