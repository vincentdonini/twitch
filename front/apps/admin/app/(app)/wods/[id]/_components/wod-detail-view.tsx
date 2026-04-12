"use client"

import { Button } from "@workspace/ui/components/button"
import { type WodDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Separator } from "@workspace/ui/components/separator"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { WodPattern } from "@workspace/ui/components/wod-pattern"
import { WodStatsCard } from "@workspace/ui/components/wod-stats-card"
import { ChevronLeft } from "lucide-react"
import { useTranslations } from "next-intl"
import ReactMarkdown from "react-markdown"
import Link from "next/link"

interface WodDetailViewProps {
  wod: WodDetail | null
  isLoading: boolean
  error: string | null
  onRetry: () => void
}

export function WodDetailView({ wod, isLoading, error, onRetry }: WodDetailViewProps) {
  const tc = useTranslations("common.wods")

  return (
    <div className="@container/main flex flex-col gap-6 px-4 lg:px-6">
      {/* Back link */}
      <div>
        <Button variant="ghost" size="sm" asChild className="-ml-2 cursor-pointer">
          <Link href="/wods">
            <ChevronLeft className="mr-1 size-4" />
            {tc("back")}
          </Link>
        </Button>
      </div>

      {/* Loading skeleton */}
      {isLoading && (
        <div className="grid grid-cols-1 lg:grid-cols-6 gap-6">
          <div className="lg:col-span-4 space-y-6">
            <Skeleton className="h-10 w-64" />
            <Skeleton className="h-6 w-48" />
            <Skeleton className="h-40 w-full" />
          </div>
          <div className="lg:col-span-2">
            <Skeleton className="h-64 w-full" />
          </div>
        </div>
      )}

      {/* Error state */}
      {!isLoading && error && (
        <div className="flex flex-col items-center justify-center py-16 gap-4 text-center">
          <p className="text-destructive">{error}</p>
          <Button onClick={onRetry} variant="outline">{tc("back")}</Button>
        </div>
      )}

      {/* Content */}
      {!isLoading && !error && wod && (
        <div className="grid grid-cols-1 lg:grid-cols-6 gap-6">

          {/* Main content */}
          <div className="lg:col-span-4 space-y-8">

            {/* Header */}
            <div className="space-y-3">
              <h1 className="text-3xl font-black">{wod.name}</h1>

              <div className="flex flex-wrap gap-1.5">
                <Badge color="primary" className="text-xs">
                  {wod.category.title}
                </Badge>
                <Badge className="text-xs">
                  {wod.type.title}
                </Badge>
                {wod.teamSize && wod.teamSize > 1 && (
                  <Badge color="secondary" className="text-xs">
                    {tc("team")} ×{wod.teamSize}
                  </Badge>
                )}
              </div>

              {wod.summary && (
                <div className="text-muted-foreground text-base">
                  <ReactMarkdown>
                    {wod.summary}
                  </ReactMarkdown>
                </div>
              )}
            </div>

            {/* Exercise pattern */}
            {wod.variants.length > 0 && (
              <WodPattern variants={wod.variants} />
            )}

            {/* Text sections */}
            {(wod.details || wod.rules || wod.tips) && (
              <div className="space-y-8">
                <Separator />
                {wod.details && (
                  <TextSection title={tc("details")} content={wod.details} />
                )}
                {wod.rules && (
                  <TextSection title={tc("rules")} content={wod.rules} />
                )}
                {wod.tips && (
                  <TextSection title={tc("tips")} content={wod.tips} />
                )}
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-2">
            <WodStatsCard
              exercises={uniqueExercises(wod)}
              exerciseCategories={uniqueExerciseCategories(wod)}
              equipments={uniqueEquipments(wod)}
              muscles={uniqueMuscles(wod)}
              divisions={uniqueDivisions(wod)}
            />
          </div>

        </div>
      )}
    </div>
  )
}

function TextSection({ title, content }: { title: string; content: string }) {
  return (
    <div className="space-y-3">
      <h2 className="text-xl font-semibold">{title}</h2>
      <div className="prose text-sm text-muted-foreground leading-relaxed">
        <ReactMarkdown>
          {content}
        </ReactMarkdown>
      </div>
    </div>
  )
}

function uniqueExercises(wod: WodDetail) {
  return [
    ...new Map(
      wod.variants.flatMap(v => v.exercises.map(e => [e.exercise.id, e.exercise])),
    ).values(),
  ]
}

function uniqueExerciseCategories(wod: WodDetail) {
  return [
    ...new Map(
      uniqueExercises(wod)
        .filter(e => e.exerciseCategory != null)
        .map(e => [e.exerciseCategory!.id, e.exerciseCategory!]),
    ).values(),
  ]
}

function uniqueEquipments(wod: WodDetail) {
  return [
    ...new Map(
      uniqueExercises(wod)
        .filter(e => e.equipment != null)
        .map(e => [e.equipment!.id, e.equipment!]),
    ).values(),
  ]
}

function uniqueMuscles(wod: WodDetail) {
  return [
    ...new Map(
      uniqueExercises(wod)
        .flatMap(e => e.muscles)
        .map(m => [m.id, m]),
    ).values(),
  ]
}

function uniqueDivisions(wod: WodDetail): string[] {
  return [...new Map(wod.variants.map(v => [v.division.id, v.division.title])).values()]
}
