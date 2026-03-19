"use client"

import { WodStats } from "@/app/(main)/wods/[id]/_components/wod-stats"
import { WodLeaderboard } from "@/app/(main)/wods/[id]/_components/wod-leaderboard"
import type { WodDetail } from "@workspace/api"

export function WodSidebar({ wod }: { wod: WodDetail }) {
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
      <div className="order-2 md:order-1">
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
      <div className="order-1 md:order-2">
        <WodLeaderboard
          wodId={wod.id}
          variants={wod.variants}

        />
      </div>
    </div>
  )
}
