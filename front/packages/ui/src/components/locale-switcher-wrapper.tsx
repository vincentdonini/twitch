"use client"

import { LocaleSwitcher } from "./locale-switcher"
import { useRouter } from "next/navigation"
import { useTransition } from "react"

interface LocaleSwitcherWrapperProps {
  setLocale: (locale: string) => Promise<void>
  locales: readonly string[]
  currentLocale: string
}

export function LocaleSwitcherWrapper({ setLocale, locales, currentLocale }: LocaleSwitcherWrapperProps) {
  const locale = currentLocale
  const router = useRouter()
  const [isPending, startTransition] = useTransition()

  const localeOptions = locales.map((l) => ({ value: l, label: l.toUpperCase() }))

  const handleChange = (newLocale: string) => {
    startTransition(async () => {
      await setLocale(newLocale)
      router.refresh()
    })
  }

  return (
    <LocaleSwitcher
      locale={locale}
      locales={localeOptions}
      onLocaleChange={handleChange}
      disabled={isPending}
    />
  )
}
