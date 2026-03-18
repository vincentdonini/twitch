"use client"

import { LeaderboardDivision } from "@/app/(main)/wods/[id]/_components/wod-leaderboard/leaderboard-division"
import type { WodDetail } from "@workspace/api"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { useTranslations } from "next-intl"

interface WodLeaderboardProps {
  wodId: string
  variants: WodDetail["variants"]
}

export function WodLeaderboard(
  {
    wodId,
    variants,
  }: WodLeaderboardProps,
) {
  const t = useTranslations("wods")
  const divisions = [...new Map(variants.map(v => [v.division.id, v.division])).values()]

  if (divisions.length === 0) return null

  return (
    <Card className="shadow-none">
      <CardHeader className="pb-3">
        <CardTitle className="text-base">{t("leaderboard")}</CardTitle>
      </CardHeader>
      <CardContent className="px-0 pb-4">
        <Tabs defaultValue={divisions[0]!.id}>
          <div className="px-4 mb-3">
            <TabsList className="w-full">
              {divisions.map(d => (
                <TabsTrigger key={d.id} value={d.id} className="flex-1">
                  {d.title}
                </TabsTrigger>
              ))}
            </TabsList>
          </div>

          {divisions.map(d => (
            <TabsContent key={d.id} value={d.id} className="mt-0">
              <LeaderboardDivision
                wodId={wodId}
                divisionId={d.id}
                variants={variants.filter(v => v.division.id === d.id)}
              />
            </TabsContent>
          ))}
        </Tabs>
      </CardContent>
    </Card>
  )
}
