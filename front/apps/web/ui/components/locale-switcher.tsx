"use client"

import { LocaleSwitcherWrapper } from "@workspace/ui/components/locale-switcher-wrapper"
import { setLocale } from "@/lib/locale-action"
import { locales } from "@/i18n/locales"
import { useLocale } from "next-intl"

export function LocaleSwitcher() {
  const currentLocale = useLocale()
  return <LocaleSwitcherWrapper setLocale={setLocale} locales={locales} currentLocale={currentLocale} />
}
