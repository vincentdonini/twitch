"use client"

import { OptionState } from "@/app/(main)/wods/_lib/filters"
import { Badge } from "@workspace/ui/components/badge"
import { Checkbox } from "@workspace/ui/components/checkbox"

interface TriStateCheckboxProps {
  label: string
  count?: number
  state: OptionState
  onToggle: () => void
}

export function TriStateCheckbox({ label, count, state, onToggle }: TriStateCheckboxProps) {
  const id = `filter-${label}`
  return (
    <div className="flex items-center gap-3">
      <Checkbox id={id} state={state} onToggle={onToggle} />
      <label
        htmlFor={id}
        className={`flex flex-1 cursor-pointer items-center justify-between text-sm font-medium ${state === "exclude" ? "text-destructive line-through" : ""}`}
      >
        {label}
        {count !== undefined && (
          <Badge color="secondary" className="text-xs font-normal">{count}</Badge>
        )}
      </label>
    </div>
  )
}
