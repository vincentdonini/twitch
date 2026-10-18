"use client"

import type { BenchmarkDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { Info, TrendingUp } from "lucide-react"
import { useTranslations } from "next-intl"
import ReactMarkdown from "react-markdown"
import { BenchmarkInformationsTab } from "./benchmark-informations-tab"
import { BenchmarkPerformanceTab } from "./benchmark-performance-tab"

interface BenchmarkContentProps {
  benchmark: BenchmarkDetail
}

export function BenchmarkContent({ benchmark }: BenchmarkContentProps) {
  const tc = useTranslations("common.benchmarks")

  return (
    <div className="flex flex-col gap-6">

      {/* Header */}
      <div className="space-y-3">
        <h1 className="text-3xl font-black leading-tight">{benchmark.title || benchmark.name}</h1>
        <div className="flex flex-wrap gap-1.5">
          <Badge color="primary" className="text-xs">{benchmark.category}</Badge>
          {benchmark.exercise?.title && (
            <Badge color="secondary" className="text-xs">{benchmark.exercise.title}</Badge>
          )}
        </div>
      </div>

      {/* Summary */}
      {benchmark.summary && (
        <div className="text-muted-foreground text-base leading-relaxed">
          <ReactMarkdown>{benchmark.summary}</ReactMarkdown>
        </div>
      )}

      {/* Tabs */}
      <Tabs defaultValue="information">
        <TabsList variant="line" className="w-full flex justify-start">
          <TabsTrigger value="information" className="cursor-pointer gap-1.5 text-lg">
            <Info className="size-3.5" />
            {tc("tab_information")}
          </TabsTrigger>
          <TabsTrigger value="my_performance" className="cursor-pointer gap-1.5 text-lg">
            <TrendingUp className="size-3.5" />
            {tc("tab_my_performance")}
          </TabsTrigger>
        </TabsList>

        {/* Information */}
        <TabsContent value="information" className="mt-6">
          <BenchmarkInformationsTab benchmark={benchmark} />
        </TabsContent>

        {/* My performance */}
        <TabsContent value="my_performance" className="mt-6">
          <BenchmarkPerformanceTab benchmark={benchmark} />
        </TabsContent>
      </Tabs>
    </div>
  )
}
