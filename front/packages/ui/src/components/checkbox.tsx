"use client"

import * as React from "react"
import * as CheckboxPrimitive from "@radix-ui/react-checkbox"
import { CheckIcon, BanIcon } from "lucide-react"
import { cn } from "@workspace/ui/lib/utils"

type CheckboxState = "include" | "exclude" | "none"

interface Props extends Omit<React.ComponentProps<typeof CheckboxPrimitive.Root>, "checked" | "onCheckedChange"> {
  state: CheckboxState
  onToggle?: () => void
  id: string
}

const stateToChecked = (state: CheckboxState): boolean | "indeterminate" => {
  if (state === "include") return true
  if (state === "exclude") return "indeterminate"
  return false
}

function Checkbox({ id, state, onToggle, className, ...props }: Props) {
  return (
    <CheckboxPrimitive.Root
      id={id}
      checked={stateToChecked(state)}
      onCheckedChange={onToggle}
      data-slot="checkbox"
      data-state-custom={state}
      className={cn(
        // base
        "peer size-4 shrink-0 rounded-[4px] border shadow-xs outline-none transition-shadow",
        "focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring",
        "disabled:cursor-not-allowed disabled:opacity-50",
        "aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40",
        // none / unchecked
        "border-input dark:bg-input/30",
        // include / checked
        "data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary",
        "dark:data-[state=checked]:bg-primary",
        // exclude / indeterminate
        "data-[state=indeterminate]:bg-destructive data-[state=indeterminate]:text-destructive-foreground data-[state=indeterminate]:border-destructive",
        className,
      )}
      {...props}
    >
      <CheckboxPrimitive.Indicator
        data-slot="checkbox-indicator"
        className="flex items-center justify-center text-current transition-none"
      >
        {state === "include" && <CheckIcon className="size-3.5" />}
        {state === "exclude" && <BanIcon className="size-2.5 text-white" />}
      </CheckboxPrimitive.Indicator>
    </CheckboxPrimitive.Root>
  )
}

export { Checkbox, type CheckboxState }
