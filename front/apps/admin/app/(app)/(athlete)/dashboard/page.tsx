"use client"

import { useState } from "react"
import { useAuth } from "@workspace/api"
import { KpiCards } from "./components/kpi-cards"
import { WodWithLeaderboard } from "./components/wod-with-leaderboard"
import { RecentPerformances } from "./components/recent-performances"
import { RecentBenchmarkCard } from "./components/recent-benchmark-card"
import { AchievementsCard } from "./components/achievements-card"
import { GoalsCard } from "./components/goals-card"
import { JoinBoxCta } from "./components/join-box-cta"
import { GymSelector } from "./components/gym-selector"

export default function Page() {
  const { user } = useAuth()
  const gyms = user?.gymSubscriptions ?? []
  const hasGym = gyms.length > 0

  const [activePlaceId, setActivePlaceId] = useState<string>(gyms[0]?.placeId ?? "")
  const activeGym = gyms.find((g) => g.placeId === activePlaceId) ?? gyms[0]

  return (
    <div className="flex flex-col gap-6 px-4 lg:px-6">
      {/* Header */}
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Dashboard</h1>
          <p className="text-muted-foreground mt-1">
            Welcome back, {user?.firstName ?? "Athlete"} 👋
          </p>
        </div>
        {hasGym && (
          <GymSelector
            gyms={gyms}
            value={activePlaceId}
            onChange={setActivePlaceId}
          />
        )}
      </div>

      {/* Row 1 — KPI cards */}
      <KpiCards />

      {/* Row 2 — WOD du jour + Leaderboard */}
      <WodWithLeaderboard />

      {/* Row 3 — Recent perfs + benchmarks + achievements */}
      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <RecentPerformances />
        <RecentBenchmarkCard />
        <AchievementsCard />
      </div>
    </div>
  )
}
