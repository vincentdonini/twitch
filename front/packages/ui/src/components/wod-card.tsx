"use client"

import { formatDate } from "@workspace/ui/lib/date"
import {
  formatScore,
  getPRScore,
  resolveMetricFromType,
  getTrend,
  type Wod,
  type WodScore,
  type ScoreEntry,
  type Trend,
} from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent, CardHeader } from "@workspace/ui/components/card"
import { ArrowRight, Minus, TrendingDown, TrendingUp, Trophy } from "lucide-react"
import { useLocale, useTranslations } from "next-intl"
import Link from "next/link"
import ReactMarkdown from "react-markdown"

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

interface WodCardProps {
  wod: Wod,
  scores?: WodScore[]
}

export function WodCard(
  {
    wod,
    scores = [],
  }: WodCardProps,
) {
  const locale = useLocale()
  const t = useTranslations("common.wods")
  const tc = useTranslations("common")

  const metric = resolveMetricFromType(wod.type.allowedMetrics[0] ?? "repetitions")
  const pr = scores.length ? getPRScore(scores as ScoreEntry[], metric) : null
  const trend = getTrend(scores as ScoreEntry[], metric)

  return (
    <Link href={`/wods/${wod.id}`}>
      <Card className="h-full overflow-hidden transition-shadow hover:shadow-md cursor-pointer">
        <CardHeader>
          <div className="space-y-3">
            <div className="flex flex-wrap gap-1.5">
              <Badge color="primary" className="text-xs">
                {wod.category.title}
              </Badge>
              <Badge className="text-xs">
                {wod.type.title}
              </Badge>
              {wod.teamSize && wod.teamSize > 1 && (
                <Badge color="secondary" className="text-xs">
                  {t("team")} ×{wod.teamSize}
                </Badge>
              )}
            </div>

            <h3 className="text-xl font-bold leading-snug mb-0">
              {wod.name}
            </h3>
            {wod.title && wod.title !== wod.name && (
              <div className="text-muted-foreground text-xs tracking-widest uppercase">
                {wod.title}
              </div>
            )}
          </div>
        </CardHeader>

        <CardContent className="flex flex-col gap-4 flex-1">
          <p className="text-muted-foreground line-clamp-2 text-sm/5">
            <ReactMarkdown>
              {wod.summary}
            </ReactMarkdown>
          </p>

          <div className="flex items-center justify-between pt-3 border-t">
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
