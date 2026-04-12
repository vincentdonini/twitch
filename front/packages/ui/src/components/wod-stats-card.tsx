"use client"

import {
  type EquipmentSummary,
  type ExerciseCategorySummary,
  type ExerciseSummary,
  type MuscleSummary,
} from "@workspace/api"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import { useTranslations } from "next-intl"
import { MuscleMap } from "./muscle-map"

type StatItem = ExerciseSummary | ExerciseCategorySummary | EquipmentSummary | MuscleSummary

function StatItemList({ label, items, emptyMessage }: { label: string; items: StatItem[]; emptyMessage: string }) {
  return (
    <div className="space-y-1.5">
      <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{label}</p>
      {items.length === 0
        ? <p className="text-xs text-muted-foreground">{emptyMessage}</p>
        : (
          <ul>
            {items.map(item => (
              <li key={item.id} className="text-xs">
                {item.title}
              </li>
            ))}
          </ul>
        )
      }
    </div>
  )
}

interface WodStatsCardProps {
  exercises: ExerciseSummary[]
  exerciseCategories: ExerciseCategorySummary[]
  divisions: string[]
  equipments: EquipmentSummary[]
  muscles: MuscleSummary[]
}

export function WodStatsCard({ exercises, exerciseCategories, divisions, equipments, muscles }: WodStatsCardProps) {
  const t = useTranslations("common.wods")

  return (
    <Card className="shadow-none">
      <CardHeader>
        <CardTitle className="text-base">{t("information")}</CardTitle>
      </CardHeader>
      <CardContent className="space-y-4">
        <div className="space-y-3">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("muscles")}</p>
          {muscles.length === 0
            ? <p className="text-sm text-muted-foreground">{t("no_muscle")}</p>
            : (
              <>
                <MuscleMap activeMuscles={muscles} />
                <ul>
                  {muscles.map(m => (
                    <li key={m.id} className="text-xs">
                      {m.title}
                    </li>
                  ))}
                </ul>
              </>
            )
          }
        </div>

        <Separator />

        <div className="space-y-1.5">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{t("divisions")}</p>
          {divisions.length === 0
            ? <p className="text-sm text-muted-foreground">—</p>
            : <ul>
              {divisions.map((d, index) => (
                <li key={index} className="text-xs">
                  {d}
                </li>
              ))}
            </ul>
          }
        </div>

        <Separator />

        <StatItemList label={t("exercise_categories")}
                      items={exerciseCategories}
                      emptyMessage={t("no_exercise_category")} />
        <Separator />

        <StatItemList label={t("exercises")}
                      items={exercises}
                      emptyMessage={t("no_exercise")} />
        <Separator />

        <StatItemList label={t("equipments")}
                      items={equipments}
                      emptyMessage={t("no_equipment")} />

      </CardContent>
    </Card>
  )
}
