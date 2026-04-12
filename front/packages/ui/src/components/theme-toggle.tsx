"use client"

import { Moon, Sun } from "lucide-react"
import { useTheme } from "next-themes"
import { Button } from "./button"
import { Kbd } from "./kbd"
import { Tooltip, TooltipContent, TooltipTrigger } from "./tooltip"
import { useTranslations } from "next-intl"

export function ThemeToggle() {
  const t = useTranslations("common.theme_toogle")
  const { resolvedTheme, setTheme } = useTheme()

  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <Button
          variant="ghost"
          size="icon"
          onClick={() => setTheme(resolvedTheme === "dark" ? "light" : "dark")}
          className="cursor-pointer"
          aria-label="Toggle theme"
        >
          <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
          <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
        </Button>
      </TooltipTrigger>
      <TooltipContent>
        {resolvedTheme === "dark" ? t("light_mode") : t("dark_mode")} <Kbd>D</Kbd>
      </TooltipContent>
    </Tooltip>



  )
}
