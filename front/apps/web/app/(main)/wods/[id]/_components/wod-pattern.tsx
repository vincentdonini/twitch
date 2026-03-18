"use client"

import { Time } from "@/lib/time"
import { ExerciseMetric, Gender, WodVariant } from "@workspace/api"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { cn } from "@workspace/ui/lib/utils"
import { useTranslations } from "next-intl"
import * as React from "react"
import { useState } from "react"

interface WodPatternProps {
  variants: WodVariant[]
}

export function WodPattern({ variants }: WodPatternProps) {
  const t = useTranslations("wods")

  const divisions = [...new Map(variants.map(v => [v.division.id, v.division])).values()]

  const [selectedGender, setSelectedGender] = useState<Gender>(
    variants[0]?.gender ?? Gender.MIXED,
  )

  const handleDivisionChange = (divisionId: string) => {
    const available = variants.filter(v => v.division.id === divisionId).map(v => v.gender)
    if (!available.includes(selectedGender)) {
      setSelectedGender(available[0] ?? Gender.MIXED)
    }
  }

  if (divisions.length === 0) return null

  return (
    <Tabs
      defaultValue={divisions[0]!.id}
      onValueChange={handleDivisionChange}
    >
      <TabsList variant="line" className="w-full flex justify-start">
        {divisions.map(d => (
          <TabsTrigger key={d.id} value={d.id}>
            {d.title}
          </TabsTrigger>
        ))}
      </TabsList>

      {divisions.map(d => {
        const divisionVariants = variants.filter(v => v.division.id === d.id)
        const availableGenders = [...new Set(divisionVariants.map(v => v.gender))]
        const activeVariant =
          divisionVariants.find(v => v.gender === selectedGender) ?? divisionVariants[0]!

        return (
          <TabsContent key={d.id} value={d.id} className="space-y-6 mt-6">

            {/* Gender selector */}
            {availableGenders.length > 1 && (
              <GenderSelector
                genders={availableGenders}
                selected={selectedGender}
                onChange={setSelectedGender}
                t={t}
              />
            )}

            {/* Rounds / time cap */}
            {(activeVariant.rounds != null || activeVariant.timeCap != null) && (
              <div className="flex flex-wrap items-center gap-3 text-sm font-medium text-muted-foreground">
                {activeVariant.rounds != null && (
                  <span>{t("rounds", { count: activeVariant.rounds })}</span>
                )}
                {activeVariant.rounds != null && activeVariant.timeCap != null && (
                  <span className="text-border">—</span>
                )}
                {activeVariant.timeCap != null && (
                  <span>
                    {t("time_cap", { value: Time.formatSecondsToDynamicHms(activeVariant.timeCap) })}
                  </span>
                )}
              </div>
            )}

            {/* Exercises */}
            <ExerciseList variant={activeVariant} t={t} />
          </TabsContent>
        )
      })}
    </Tabs>
  )
}

// -----------------------------------------------------------------------------
// Gender selector
// -----------------------------------------------------------------------------

interface GenderSelectorProps {
  genders: Gender[]
  selected: Gender
  onChange: (g: Gender) => void
  t: ReturnType<typeof useTranslations<"wods">>
}

function GenderSelector(
  {
    genders,
    selected,
    onChange,
    t,
  }: GenderSelectorProps,
) {
  return (
    <div className="flex gap-1 p-1 rounded-lg bg-muted w-fit">
      {genders.map(g => (
        <button
          key={g}
          onClick={() => onChange(g)}
          className={cn(
            "px-3 py-1 rounded-md text-sm font-medium transition-colors",
            selected === g
              ? "bg-background text-foreground shadow-sm"
              : "text-muted-foreground hover:text-foreground",
          )}
        >
          <span className="flex items-center gap-1.5">
            {g === Gender.MALE && <MarsIcon className="size-3.5" />}
            {g === Gender.FEMALE && <VenusIcon className="size-3.5" />}
            {g === Gender.MIXED && <VenusAndMarsIcon className="size-3.5" />}
            {t(`gender_${g}` as "gender_male" | "gender_female" | "gender_mixed")}
          </span>
        </button>
      ))}
    </div>
  )
}

// -----------------------------------------------------------------------------
// Exercise list
// -----------------------------------------------------------------------------

function ExerciseList(
  {
    variant,
    t,
  }: {
    variant: WodVariant
    t: ReturnType<typeof useTranslations<"wods">>
  },
) {
  const sorted = [...variant.exercises].sort((a, b) => a.position - b.position)

  if (sorted.length === 0) return null

  return (
    <ol>
      {sorted.map(we => {
        const repsMetric = we.metrics.find(m => m.type === "repetitions")
        const otherMetrics = we.metrics.filter(m => m.type !== "repetitions")

        return (
          <li key={we.id} className="flex items-baseline gap-4">
            <div className="min-w-0">
              <div className="font-semibold text-xl md:text-2xl inline-block">
                {repsMetric && (
                  <React.Fragment>
                    {formatMetric(repsMetric, t, { onlyValue: true }) + " "}
                  </React.Fragment>
                )}
                <React.Fragment>
                  {we.exercise.title}
                </React.Fragment>
              </div>

              {otherMetrics.length > 0 && (
                <div className="text-sm text-muted-foreground whitespace-nowrap inline-block ml-2">
                  (
                  {otherMetrics.map(m => (
                    <React.Fragment key={m.id}>
                      {formatMetric(m, t)}
                    </React.Fragment>
                  ))}
                  )
                </div>
              )}
              {/*</Stack>*/}
            </div>
          </li>
        )
      })}
    </ol>
  )
}

// -----------------------------------------------------------------------------
// Gender icons
// -----------------------------------------------------------------------------

function MarsIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
         strokeLinecap="round" strokeLinejoin="round">
      <circle cx="10" cy="14" r="5" />
      <path d="M19 5l-5.4 5.4" />
      <path d="M15 5h4v4" />
    </svg>
  )
}

function VenusIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
         strokeLinecap="round" strokeLinejoin="round">
      <circle cx="12" cy="9" r="5" />
      <path d="M12 14v6" />
      <path d="M9 19h6" />
    </svg>
  )
}

function VenusAndMarsIcon({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
         strokeLinecap="round" strokeLinejoin="round">
      <circle cx="8" cy="14" r="4" />
      <path d="M8 18v3" />
      <path d="M6 21h4" />
      <path d="M18 4l-3.5 3.5" />
      <path d="M14.5 4H18v3.5" />
      <circle cx="16" cy="10" r="3" />
    </svg>
  )
}

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------

function formatMetric(
  metric: ExerciseMetric,
  t: ReturnType<typeof useTranslations<"wods">>,
  options?: { onlyValue?: boolean },
): string {
  if (options?.onlyValue) return String(metric.value)

  const key = `metric_${metric.type}` as
    | "metric_repetitions"
    | "metric_weight"
    | "metric_time"
    | "metric_distance"
    | "metric_calories"

  return t(key, { value: metric.value })
}
