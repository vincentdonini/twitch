"use client"

import { GenderSelector } from "@/app/(main)/wods/[id]/_components/wod-pattern/gender-selector"
import { Time } from "@/lib/time"
import { Gender, WodVariant } from "@workspace/api"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { useTranslations } from "next-intl"
import { useState } from "react"

import { ExerciseList } from "./exercise-list"

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
          <TabsTrigger key={d.id} value={d.id} className="text-lg">
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

            {availableGenders.length > 1 && (
              <GenderSelector
                genders={availableGenders}
                selected={selectedGender}
                onChange={setSelectedGender}
                t={t}
              />
            )}

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

            <ExerciseList variant={activeVariant} />

          </TabsContent>
        )
      })}
    </Tabs>
  )
}
