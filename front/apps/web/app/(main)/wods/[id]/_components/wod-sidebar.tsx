"use client"

import { WodLeaderboard } from "@/app/(main)/wods/[id]/_components/wod-leaderboard"
import { WodScoreSubmit } from "@/app/(main)/wods/[id]/_components/wod-score-submit"
import { WodStats } from "@/app/(main)/wods/[id]/_components/wod-stats"
import type { WodDetail } from "@workspace/api"
import { useAuth } from "@workspace/api"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Button } from "@workspace/ui/components/button"
import { Item, ItemContent, ItemDescription, ItemMedia, ItemTitle } from "@workspace/ui/components/item"
import { TrophyIcon } from "lucide-react"
import { useTranslations } from "next-intl"
import { useState } from "react"

export function WodSidebar({ wod }: { wod: WodDetail }) {
  const t = useTranslations("wods")
  const { isAuthenticated } = useAuth()
  const [leaderboardKey, setLeaderboardKey] = useState(0)

  const uniqueExercises = [
    ...new Map(
      wod.variants.flatMap(v => v.exercises.map(e => [e.exercise.id, e.exercise])),
    ).values(),
  ]

  const uniqueExerciseCategories = [
    ...new Map(
      uniqueExercises
        .filter(e => e.exerciseCategory != null)
        .map(e => [e.exerciseCategory!.id, e.exerciseCategory!]),
    ).values(),
  ]

  const uniqueEquipments = [
    ...new Map(
      uniqueExercises
        .filter(e => e.equipment != null)
        .map(e => [e.equipment!.id, e.equipment!]),
    ).values(),
  ]

  const uniqueDivisions = [
    ...new Map(wod.variants.map(v => [v.division.id, v.division.title])).values(),
  ]

  const timeCaps = wod.variants.map(v => v.timeCap).filter((t): t is number => t != null)
  const roundsList = wod.variants.map(v => v.rounds).filter((r): r is number => r != null)
  const minTimeCap = timeCaps.length ? Math.min(...timeCaps) : null
  const maxRounds = roundsList.length ? Math.max(...roundsList) : null

  return (
    <div className="grid grid-cols-1 gap-6">
      <div className="order-3 md:order-1">
        <WodStats
          exercises={uniqueExercises}
          exerciseCategories={uniqueExerciseCategories}
          equipments={uniqueEquipments}
          divisions={uniqueDivisions}
          timeCap={minTimeCap}
          rounds={maxRounds}
          teamSize={wod.teamSize}
        />
      </div>

      {isAuthenticated && (
        <div className="order-2 md:order-2">
          <WodScoreSubmit wod={wod} onSuccess={() => setLeaderboardKey(k => k + 1)}>
            {({ open }) => (
              <Item variant="outline" className="bg-white">
                <ItemMedia>
                  <Avatar className="w-10 h-10">
                    <AvatarFallback className="flex items-center justify-center">
                      <TrophyIcon size={20} />
                    </AvatarFallback>
                  </Avatar>
                </ItemMedia>
                <ItemContent>
                  <ItemTitle>
                    {t("log_score_title")}
                  </ItemTitle>
                  <ItemDescription>
                    {t("log_score_subtitle")}
                  </ItemDescription>
                </ItemContent>
                <Button className="w-full font-bold text-md" size="lg" onClick={open}>
                  {t("log_score")}
                </Button>
              </Item>
            )}
          </WodScoreSubmit>
        </div>
      )}

      <div className="order-1 md:order-3">
        <WodLeaderboard
          wod={wod}
          refreshKey={leaderboardKey}
        />
      </div>
    </div>
  )
}
