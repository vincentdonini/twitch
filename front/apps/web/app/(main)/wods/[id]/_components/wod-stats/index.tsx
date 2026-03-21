import type { EquipmentSummary, ExerciseCategorySummary, ExerciseSummary } from "@workspace/api"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import { useTranslations } from "next-intl"

import { StatsDivisions } from "./stats-divisions"
import { StatsLinkList } from "./stats-link-list"

interface WodStatsProps {
  exercises: ExerciseSummary[]
  exerciseCategories: ExerciseCategorySummary[]
  divisions: string[]
  equipments: EquipmentSummary[]
  timeCap: number | null
  rounds: number | null
  teamSize: number | null
}

export function WodStats(
  {
    exercises,
    exerciseCategories,
    divisions,
    equipments,
  }: WodStatsProps,
) {
  const t = useTranslations("wods")

  return (
    <Card className="shadow-none">
      <CardHeader>
        <CardTitle className="text-base">
          {t("statistics")}
        </CardTitle>
        <CardDescription>
          {t("statistics_description")}
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        <StatsDivisions divisions={divisions} />

        <Separator />

        <StatsLinkList
          label={t("exercise_category")}
          items={exerciseCategories}
          hrefPrefix="/exercise-categories"
          emptyMessage={t("no_exercise_category")}
        />

        <Separator />

        <StatsLinkList
          label={t("exercise")}
          items={exercises}
          hrefPrefix="/exercises"
          emptyMessage={t("no_exercise")}
        />

        <Separator />

        <StatsLinkList
          label={t("equipment")}
          items={equipments}
          hrefPrefix="/equipments"
          emptyMessage={t("no_equipment")}
        />
      </CardContent>
    </Card>
  )
}
