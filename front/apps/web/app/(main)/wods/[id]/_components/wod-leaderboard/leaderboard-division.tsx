"use client"

import { LeaderboardList } from "@/app/(main)/wods/[id]/_components/wod-leaderboard/leaderboard-list"
import { Gender, useGetWodLeaderboard, WodDetail } from "@workspace/api"
import { cn } from "@workspace/ui/lib/utils"
import { useTranslations } from "next-intl"
import { useState } from "react"

interface LeaderboardDivisionProps {
  wodId: string
  divisionId: string
  variants: WodDetail["variants"]
}

export function LeaderboardDivision(
  {
    wodId,
    divisionId,
    variants,
  }: LeaderboardDivisionProps,
) {
  const tc = useTranslations("common")
  const t = useTranslations("wods")

  const availableGenders = [...new Set(variants.map(v => v.gender))]
  const [selectedGender, setSelectedGender] = useState<Gender>(availableGenders[0] ?? Gender.MIXED)

  const { data, isLoading } = useGetWodLeaderboard(wodId, divisionId, selectedGender)

  return (
    <div className="space-y-3">
      {availableGenders.length > 1 && (
        <div className="px-4 flex gap-1">
          {availableGenders.map(g => (
            <button
              key={g}
              onClick={() => setSelectedGender(g)}
              className={cn(
                "px-2.5 py-1 rounded-md text-xs font-medium transition-colors",
                selectedGender === g
                  ? "bg-muted text-foreground"
                  : "text-muted-foreground hover:text-foreground",
              )}
            >
              {g === Gender.MALE ? "♂" : g === Gender.FEMALE ? "♀" : "⚥"}&nbsp;{g}
            </button>
          ))}
        </div>
      )}

      {isLoading && (
        <div className="px-4 py-6 text-center text-sm text-muted-foreground">
          {tc("loading")}
        </div>
      )}
      {!isLoading && (!data || data.length === 0) && (
        <div className="px-4 py-6 text-center text-sm text-muted-foreground">
          {t("no_results_leaderboard")}
        </div>
      )}
      {!isLoading && data && data.length > 0 && (
        <LeaderboardList entries={data} />
      )}
    </div>
  )
}
