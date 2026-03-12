"use client"

import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "./select"

interface LocaleOption {
  value: string
  label: string
}

interface LocaleSwitcherProps {
  locale: string
  locales: LocaleOption[]
  onLocaleChange: (locale: string) => void
  disabled?: boolean
}

export function LocaleSwitcher({ locale, locales, onLocaleChange, disabled }: LocaleSwitcherProps) {
  return (
    <Select value={locale} onValueChange={onLocaleChange} disabled={disabled}>
      <SelectTrigger size="sm" className="w-auto gap-1 border-none shadow-none bg-transparent text-sm font-medium cursor-pointer">
        <SelectValue />
      </SelectTrigger>
      <SelectContent align="end">
        {locales.map((l) => (
          <SelectItem key={l.value} value={l.value}>
            {l.label}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  )
}
