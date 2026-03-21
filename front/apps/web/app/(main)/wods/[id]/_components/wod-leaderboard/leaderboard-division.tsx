"use client"

import { LeaderboardList } from "@/app/(main)/wods/[id]/_components/wod-leaderboard/leaderboard-list"
import { WodScoreSubmit } from "@/app/(main)/wods/[id]/_components/wod-score-submit"
import { Gender, useAuth, useGetWodLeaderboard, WodDetail } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from "@workspace/ui/components/empty"
import { Separator } from "@workspace/ui/components/separator"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Spinner } from "@workspace/ui/components/spinner"
import { Stack } from "@workspace/ui/components/stack"
import { cn } from "@workspace/ui/lib/utils"
import { TrophyIcon } from "lucide-react"
import { useTranslations } from "next-intl"
import { useEffect, useState } from "react"

interface LeaderboardDivisionProps {
  wod: WodDetail
  divisionId: string
  variants: WodDetail["variants"]
  refreshKey?: number
}

export function LeaderboardDivision({ wod, divisionId, variants, refreshKey }: LeaderboardDivisionProps) {
  const tc = useTranslations("common")
  const t = useTranslations("wods")
  const { isAuthenticated } = useAuth()

  const availableGenders = [...new Set(variants.map(v => v.gender))]
  const [selectedGender, setSelectedGender] = useState<Gender>(availableGenders[0] ?? Gender.MIXED)

  const { data, isLoading, refetch } = useGetWodLeaderboard(wod.id, divisionId, selectedGender)

  useEffect(() => {
    if (refreshKey) refetch()
  }, [refreshKey])

  return (
    <div className="px-4">
      {availableGenders.length > 1 && (
        <div className="flex gap-1 justify-between mb-4">
          {availableGenders.map(g => (
            <button
              key={g}
              onClick={() => setSelectedGender(g)}
              className={cn(
                "px-2.5 py-1 rounded-md text-xs font-medium transition-colors w-full border",
                selectedGender === g
                  ? "bg-muted text-foreground"
                  : "text-muted-foreground hover:text-foreground border-1 border-muted",
              )}
            >
              {g === Gender.MALE ? "♂" : g === Gender.FEMALE ? "♀" : "⚥"}&nbsp;{g}
            </button>
          ))}
        </div>
      )}

      {isLoading && (
        <Skeleton>
          <Stack
            align="center"
            justify="center"
            direction="vertical"
            gap={1}
            className="h-[190px] p-4 text-center text-muted-foreground"
          >
            <Spinner className="size-8" />
            {tc("loading")}
          </Stack>
        </Skeleton>
      )}

      {!isLoading && (!data || data.length === 0) && (
        <Empty className="h-full bg-muted/30">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <TrophyIcon />
            </EmptyMedia>
            <EmptyTitle>
              {t("no_results_leaderboard")}
            </EmptyTitle>
            <EmptyDescription className="max-w-xs text-pretty">
              {t("no_results_leaderboard_description")}
            </EmptyDescription>
          </EmptyHeader>
          <EmptyContent>
            {isAuthenticated
              ? (
                <WodScoreSubmit wod={wod} onSuccess={() => refetch()}>
                  {({ open }) => (
                    <Button onClick={open}>
                      {t("log_score")}
                    </Button>
                  )}
                </WodScoreSubmit>
              )
              : (
                <Button variant="outline" onClick={() => refetch()}>
                  {tc("retry")}
                </Button>
              )
            }
          </EmptyContent>
        </Empty>
      )}

      {!isLoading && data && data.length > 0 && (
        <>
          <Separator className="my-4" />
          <LeaderboardList entries={data} />
        </>
      )}
    </div>
  )
}
