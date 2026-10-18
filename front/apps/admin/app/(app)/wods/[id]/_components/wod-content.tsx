"use client"

import { type WodDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { Info, TrendingUp } from "lucide-react"
import { useTranslations } from "next-intl"
import ReactMarkdown from "react-markdown"
import { WodInformationsTab } from "./wod-informations-tab"
import { WodPerformanceTab } from "./wod-performance-tab"

interface WodContentProps {
  wod: WodDetail
}

export function WodContent({ wod }: WodContentProps) {
  const tc = useTranslations("common.wods")

  return (
    <div className="flex flex-col gap-6">

      {/* Header */}
      <div className="flex items-start justify-between gap-3">
        <div className="space-y-2">
          <h1 className="text-3xl font-black leading-tight">{wod.name}</h1>
          <div className="flex flex-wrap gap-1.5">
            <Badge color="primary" className="text-xs">{wod.category.title}</Badge>
            <Badge className="text-xs">{wod.type.title}</Badge>
            {wod.teamSize && wod.teamSize > 1 && (
              <Badge color="secondary" className="text-xs">{tc("team")} ×{wod.teamSize}</Badge>
            )}
          </div>
        </div>
      </div>

      {wod.summary && (
        <div className="text-muted-foreground text-base leading-relaxed">
          <ReactMarkdown>{wod.summary}</ReactMarkdown>
        </div>
      )}

      {/* Tabs */}
      <Tabs defaultValue="informations">
        <TabsList variant="line" className="w-full flex justify-start">
          <TabsTrigger value="informations" className="cursor-pointer gap-1.5 text-lg">
            <Info className="size-3.5" />
            {tc("information")}
          </TabsTrigger>
          <TabsTrigger value="performance" className="cursor-pointer gap-1.5 text-lg">
            <TrendingUp className="size-3.5" />
            {tc("my_performances")}
          </TabsTrigger>
        </TabsList>

        <TabsContent value="informations" className="mt-6">
          <WodInformationsTab wod={wod} />
        </TabsContent>

        <TabsContent value="performance" className="mt-6">
          <WodPerformanceTab wod={wod} />
        </TabsContent>
      </Tabs>
    </div>
  )
}
