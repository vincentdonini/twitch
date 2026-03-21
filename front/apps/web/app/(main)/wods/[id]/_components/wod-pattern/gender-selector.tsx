import { Gender } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import { cn } from "@workspace/ui/lib/utils"
import { MarsIcon, VenusAndMarsIcon, VenusIcon } from "lucide-react"
import { useTranslations } from "next-intl"
import * as React from "react"

interface GenderSelectorProps {
  genders: Gender[]
  selected: Gender
  onChange: (g: Gender) => void
  t: ReturnType<typeof useTranslations<"wods">>
}

export function GenderSelector(
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
        <Button
          key={g}
          onClick={() => onChange(g)}
          className={cn(
            "px-3 py-1 rounded-md text-sm font-medium transition-colors",
            selected === g
              ? "bg-primary text-primary-foreground"
              : "bg-background text-foreground",
          )}
        >
          <span className="flex items-center gap-1.5">
            {g === Gender.MALE && <MarsIcon className="size-3.5" />}
            {g === Gender.FEMALE && <VenusIcon className="size-3.5" />}
            {g === Gender.MIXED && <VenusAndMarsIcon className="size-3.5" />}
            {t(`gender_${g}` as "gender_male" | "gender_female" | "gender_mixed")}
          </span>
        </Button>
      ))}
    </div>
  )
}
