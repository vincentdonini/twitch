import { formatScore, getPRScore, type Metric, type ScoreEntry } from "@workspace/api"
import { CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import { formatDate } from "@workspace/ui/lib/date"
import { Trophy } from "lucide-react"
import { useLocale, useTranslations } from "next-intl"

interface ScorePrProps {
  scores: ScoreEntry[]
  metrics: Metric[]
  onScoreLogged: () => void
  renderLogDrawer: (onSuccess: () => void, fullWidth?: boolean) => React.ReactNode
}

export function ScorePr(
  {
    scores,
    metrics,
    onScoreLogged,
    renderLogDrawer,
  }: ScorePrProps,
) {
  const locale = useLocale()
  const tc = useTranslations("common.performance")

  const prScore = getPRScore(scores, metrics)

  return (
    <>
      <CardHeader>
        <CardTitle className="text-base flex items-center gap-2">
          <Trophy className="size-4 text-yellow-500" />
          {tc("personal_record")}
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-4">
        <div>
          <p className="text-3xl font-black tabular-nums">
            {formatScore(prScore)}
          </p>
          <p className="text-xs text-muted-foreground mt-0.5">
            {formatDate(prScore.performedAt, locale, { year: "numeric", month: "long", day: "numeric" })}
          </p>
        </div>
        <Separator />
        <div className="flex flex-col gap-1.5 text-sm">
          <div className="flex justify-between">
            <span className="text-muted-foreground">{tc("attempts")}</span>
            <span className="font-medium">{scores.length}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">{tc("last_attempt")}</span>
            <span className="font-medium">
              {formatDate(scores[scores.length - 1]!.performedAt, locale, {
                year: "numeric",
                month: "long",
                day: "numeric",
              })}
            </span>
          </div>
        </div>
        {renderLogDrawer(onScoreLogged, true)}
      </CardContent>
    </>
  )
}
