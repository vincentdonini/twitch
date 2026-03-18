import type { EquipmentSummary, ExerciseCategorySummary, ExerciseSummary, WodDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { useTranslations } from "next-intl"

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
      <CardHeader className="pb-3">
        <CardTitle className="text-base">{t("statistics")}</CardTitle>
      </CardHeader>
      <CardContent className="space-y-4">

        {/* Divisions */}
        <div className="space-y-1.5">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("divisions")}</p>
          <div className="flex flex-wrap gap-1.5">
            {divisions.map(d => (
              <Badge key={d} color="secondary" className="text-xs">{d}</Badge>
            ))}
          </div>
        </div>

        {/* Exercise Categories list */}
        <div className="space-y-1.5">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("exercise_category")}</p>
          <ul className="space-y-1">
            {exerciseCategories.map(exerciseCategory => (
              <li key={exerciseCategory.id} className="flex items-center gap-2 text-sm">
                <span className="size-1.5 rounded-full bg-muted-foreground/40 shrink-0" />
                <a className="text-sm" href={`/exercise-categories/${exerciseCategory.id}`}
                   title={exerciseCategory.title}>{exerciseCategory.title}</a>
              </li>
            ))}
          </ul>
        </div>

        {/* Exercises list */}
        <div className="space-y-1.5">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("exercise")}</p>
          <ul className="space-y-1">
            {exercises.map(exercise => (
              <li key={exercise.id} className="flex items-center gap-2 text-sm">
                <span className="size-1.5 rounded-full bg-muted-foreground/40 shrink-0" />
                <a className="text-sm" href={`/exercises/${exercise.id}`}
                   title={exercise.title}>{exercise.title}</a>
              </li>
            ))}
          </ul>
        </div>

        {/* Equipment list */}
        <div className="space-y-1.5">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("equipment")}</p>
          <ul className="space-y-1">
            {equipments.map(equipment => (
              <li key={equipment.id} className="flex items-center gap-2 text-sm">
                <span className="size-1.5 rounded-full bg-muted-foreground/40 shrink-0" />
                <a className="text-sm" href={`/equipments/${equipment.id}`}
                   title={equipment.title}>{equipment.title}</a>
              </li>
            ))}
          </ul>
        </div>

      </CardContent>
    </Card>
  )
}
