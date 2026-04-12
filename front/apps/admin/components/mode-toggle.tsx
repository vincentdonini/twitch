"use client"

import * as React from "react"
import { Moon, Sun } from "lucide-react"

import { Button } from "@workspace/ui/components/button"
import { useTheme } from "@workspace/ui/hooks/use-theme"
import { useCircularTransition } from "@workspace/ui/hooks/use-circular-transition"

interface ModeToggleProps {
  variant?: "outline" | "ghost" | "default"
}

export function ModeToggle({ variant = "outline" }: ModeToggleProps) {
  const { resolvedTheme } = useTheme()
  const { toggleTheme } = useCircularTransition()

  const isDarkMode = resolvedTheme === "dark"

  const handleToggle = (event: React.MouseEvent<HTMLButtonElement>) => {
    toggleTheme(event)
  }

  return (
    <Button
      variant={variant}
      size="icon"
      onClick={handleToggle}
      className="cursor-pointer mode-toggle-button relative overflow-hidden"
    >
      {/* Show the icon for the mode you can switch TO */}
      {isDarkMode ? (
        <Sun className="h-[1.2rem] w-[1.2rem] transition-transform duration-300 rotate-0 scale-100" />
      ) : (
        <Moon className="h-[1.2rem] w-[1.2rem] transition-transform duration-300 rotate-0 scale-100" />
      )}
      <span className="sr-only">
        Switch to {isDarkMode ? "light" : "dark"} mode
      </span>
    </Button>
  )
}
