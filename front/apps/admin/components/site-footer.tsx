"use client"

import { Anvil, Dumbbell, Heart, Rocket } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"
import * as React from "react"

const VARIANTS = [
  { key: "love",    Icon: Heart },
  { key: "athlete", Icon: Dumbbell },
  { key: "rocket",  Icon: Rocket },
  { key: "anvil",   Icon: Anvil },
] as const

export function SiteFooter() {
  const t = useTranslations("footer")
  const [idx] = React.useState(() => Math.floor(Math.random() * VARIANTS.length))
  const { key, Icon } = VARIANTS[idx]

  const icon = () => <Icon className="h-4 w-4 fill-red-500 text-red-500" />
  const link = (chunks: React.ReactNode) => (
    <Link
      href="https://www.vincentdonini.com"
      target="_blank"
      rel="noopener noreferrer"
      className="font-medium text-foreground hover:text-primary transition-colors"
    >
      {chunks}
    </Link>
  )

  return (
    <footer className="border-t bg-background">
      <div className="px-4 py-6 lg:px-6">
        <div className="flex flex-col items-center justify-center space-y-2 text-center">
          <p className="flex items-center justify-center gap-1 text-sm text-muted-foreground">
            {t.rich(`${key}.signature` as never, { icon, link })}
          </p>
          <p className="text-xs text-muted-foreground">
            {t(`${key}.tagline` as never)}
          </p>
        </div>
      </div>
    </footer>
  )
}
