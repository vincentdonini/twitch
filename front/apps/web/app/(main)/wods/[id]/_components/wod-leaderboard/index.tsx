"use client"

import { LeaderboardDivision } from "@/app/(main)/wods/[id]/_components/wod-leaderboard/leaderboard-division"
import { type WodDetail } from "@workspace/api"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { useTranslations } from "next-intl"
import * as React from "react"

interface WodLeaderboardProps {
  wod: WodDetail
  refreshKey?: number
}

export function WodLeaderboard({ wod, refreshKey }: WodLeaderboardProps) {
  const t = useTranslations("wods")
  const divisions = [...new Map(wod.variants.map(v => [v.division.id, v.division])).values()]

  if (divisions.length === 0) return null

  return (
    <Card className="shadow-none">
      <CardHeader>
        <CardTitle className="text-balance">
          {t("leaderboard")}
        </CardTitle>
        <CardDescription>
          {t("leaderboard_description")}
        </CardDescription>
      </CardHeader>
      <CardContent className="px-0">
        <Tabs defaultValue={divisions[0]!.id}>
          <div className="px-4">
            <TabsList className="w-full">
              {divisions.map(d => (
                <TabsTrigger key={d.id} value={d.id} className="flex-1">
                  {d.title}
                </TabsTrigger>
              ))}
            </TabsList>
          </div>

          {divisions.map(d => (
            <TabsContent key={d.id} value={d.id}>
              <LeaderboardDivision
                wod={wod}
                divisionId={d.id}
                variants={wod.variants.filter(v => v.division.id === d.id)}
                refreshKey={refreshKey}
              />
            </TabsContent>
          ))}
        </Tabs>
      </CardContent>
    </Card>
  )
}
