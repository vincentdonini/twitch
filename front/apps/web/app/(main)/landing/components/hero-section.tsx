"use client"

import Link from "next/link"
import { ArrowRight, Play, Star } from "lucide-react"
import { Button } from "@workspace/ui/components/button"
import { Badge } from "@workspace/ui/components/badge"
import { DotPattern } from "@workspace/ui/components/dot-pattern"
import { useTranslations } from "next-intl"

export function HeroSection() {
  const t = useTranslations("landing.hero")

  return (
    <section
      id="hero"
      className="py-24"
    >
      {/* Background Pattern */}
      {/* ---------------------------------------------------------------------------------------------------------- */}
      <div className="absolute inset-0">
        <DotPattern className="opacity-100" size="md" fadeStyle="ellipse" />
      </div>

      <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div className="mx-auto max-w-4xl text-center">

          {/* Announcement Badge */}
          {/* ------------------------------------------------------------------------------------------------------ */}
          <div className="mb-8 flex justify-center">
            <Badge variant="outline" className="px-4 py-2 border-foreground">
              <Star className="w-3 h-3 mr-2 fill-current" />
              {t("badge")}
              <ArrowRight className="w-3 h-3 ml-2" />
            </Badge>
          </div>

          {/* Main Headline */}
          {/* ------------------------------------------------------------------------------------------------------ */}
          <h1 className="mb-6 font-bold tracking-tight text-4xl sm:text-6xl lg:text-7xl">
            {t.rich("title", {
              gradient: (chunks) => (
                <span
                  className="text-4xl sm:text-6xl lg:text-7xl bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                  {chunks}
                </span>
              ),
            })}
          </h1>

          {/* Subheading */}
          {/* ------------------------------------------------------------------------------------------------------ */}
          <p className="mx-auto mb-10 max-w-2xl text-lg text-muted-foreground sm:text-xl">
            {t("description")}
          </p>

          {/* CTA Buttons */}
          {/* ------------------------------------------------------------------------------------------------------ */}
          <div className="flex flex-col gap-4 sm:flex-row sm:justify-center">
            <Button size="lg" className="text-base cursor-pointer" asChild>
              <Link href="/sign-up">
                {t("cta_primary")}
                <ArrowRight className="ml-2 h-4 w-4" />
              </Link>
            </Button>

            <Button variant="outline" size="lg" className="text-base cursor-pointer" asChild>
              <Link href="/wods">
                <Play className="mr-2 h-4 w-4" />
                {t("cta_secondary")}
              </Link>
            </Button>
          </div>

        </div>
      </div>
    </section>
  )
}
