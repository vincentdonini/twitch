"use client"

import { useGetMuscleAreas } from "@workspace/api"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { useEffect } from "react"
import type { OptionState } from "./types"
import { WodTriStateCheckbox } from "./wod-tri-state-checkbox"

interface WodMuscleAreaFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function WodMuscleAreaFilter({ getState, onToggle, onLabelsDiscovered }: WodMuscleAreaFilterProps) {
  const { data: muscleAreas, isLoading } = useGetMuscleAreas({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!muscleAreas?.length) return
    const discovered: Record<string, string> = {}
    muscleAreas.forEach(a => { discovered[a.id] = a.title })
    onLabelsDiscovered?.(discovered)
  }, [muscleAreas]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <div className="space-y-2">
      {isLoading
        ? Array.from({ length: 3 }).map((_, i) => (
          <div key={i} className="flex items-center gap-3">
            <Skeleton className="size-4 rounded-[4px]" />
            <Skeleton className="h-4 w-full rounded" />
          </div>
        ))
        : (muscleAreas ?? []).map(area => (
          <WodTriStateCheckbox
            key={area.id}
            label={area.title}
            count={area.wodCount}
            state={getState(area.id)}
            onToggle={() => onToggle(area.id)}
          />
        ))
      }
    </div>
  )

}
