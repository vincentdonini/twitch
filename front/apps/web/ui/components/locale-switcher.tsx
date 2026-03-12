"use client"

import { useLocale } from "next-intl"
import { useRouter } from "next/navigation"
import { useTransition } from "react"
import { setLocale } from "@/lib/locale-action"
import { locales, type Locale } from "@/i18n/locales"
import { LocaleSwitcher as LocaleSwitcherUI } from "@workspace/ui/components/locale-switcher"

const localeOptions = locales.map((l) => ({ value: l, label: l.toUpperCase() }))

export function LocaleSwitcher() {
  const locale = useLocale()
  const router = useRouter()
  const [isPending, startTransition] = useTransition()

  const handleChange = (newLocale: string) => {
    startTransition(async () => {
      await setLocale(newLocale as Locale)
      router.refresh()
    })
  }

  return (
    <LocaleSwitcherUI
      locale={locale}
      locales={localeOptions}
      onLocaleChange={handleChange}
      disabled={isPending}
    />
  )
}
