"use client"

import { Newspaper } from "lucide-react"
import { Card, CardContent } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { useTranslations } from "next-intl"

export function BlogComingSoonSection() {
  const t = useTranslations("landing.blog")

  return (
    <section
      id="blog"
      className="py-24 sm:py-32 bg-muted/50"
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">

        {/* Section Header */}
        <div className="mx-auto max-w-2xl text-center mb-12">
          <Badge variant="outline" className="mb-4">
            {t("badge")}
          </Badge>

          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl mb-4">
            {t("title")}
          </h2>

          <p className="text-lg text-muted-foreground">
            {t("description")}
          </p>
        </div>

        {/* Coming Soon Card */}
        <div className="flex justify-center">
          <Card className="max-w-lg text-center bg-background/60 backdrop-blur-sm border-border/50">
            <CardContent className="p-10 space-y-4">

              <div className="flex justify-center">
                <div className="p-4 bg-primary/10 rounded-xl">
                  <Newspaper className="h-8 w-8 text-primary" />
                </div>
              </div>

              <Badge variant="secondary">{t("coming_soon_badge")}</Badge>

              <h3 className="text-xl font-bold">
                {t("coming_soon_title")}
              </h3>

              <p className="text-muted-foreground">
                {t("coming_soon_description")}
              </p>

            </CardContent>
          </Card>
        </div>

      </div>
    </section>
  )
}