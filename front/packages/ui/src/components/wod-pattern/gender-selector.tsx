"use client"

import { Gender } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { useTranslations } from "next-intl"

const GENDER_KEYS = {
  [Gender.Male]: "gender_male",
  [Gender.Female]: "gender_female",
  [Gender.Mixed]: "gender_mixed",
} as const satisfies Record<Gender, string>

interface GenderSelectorProps {
  genders: Gender[]
  selected: Gender
  onChange: (g: Gender) => void
  t: ReturnType<typeof useTranslations<"common.wods">>
}

export function GenderSelector({ genders, selected, onChange, t }: GenderSelectorProps) {
  return (
    <div className="flex flex-wrap gap-2">
      {genders.map(g => (
        <Badge
          key={g}
          color={selected === g ? "primary" : "secondary"}
          className="cursor-pointer select-none"
          onClick={() => onChange(g)}
        >
          {t(GENDER_KEYS[g] as "gender_male" | "gender_female" | "gender_mixed")}
        </Badge>
      ))}
    </div>
  )
}
