"use client"

import { Badge } from "@workspace/ui/components/badge"
import { Checkbox } from "@workspace/ui/components/checkbox"
import { OptionState } from "./types"

interface TriStateCheckboxProps {
  label: string
  count?: number
  state: OptionState
  onToggle: () => void
}

export function WodTriStateCheckbox(
  {
    label,
    count,
    state,
    onToggle,
  }: TriStateCheckboxProps,
) {
  const id = `wod-filter-${label}`
  return (
    <div className="flex items-center gap-3 overflow-hidden">
      <Checkbox id={id} state={state} onToggle={onToggle} />
      <label
        htmlFor={id}
        className={`flex-1 truncate text-sm font-medium cursor-pointer ${
          state === OptionState.Exclude ? "text-destructive line-through" : ""
        }`}
        onClick={() => document.getElementById(id)?.click()}
      >
        {label}
      </label>
      {count !== undefined && (
        <Badge color="secondary" className="text-xs font-normal">
          {count}
        </Badge>
      )}
    </div>
  )
}
