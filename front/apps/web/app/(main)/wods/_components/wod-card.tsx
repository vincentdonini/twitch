"use client"

import type { Wod } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent } from "@workspace/ui/components/card"
import { ArrowRight } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"

export function WodCard({ wod }: { wod: Wod }) {
  const t = useTranslations("wods")

  return (
    <Link href={`/wods/${wod.id}`}>
      <Card className="h-full overflow-hidden py-0 transition-shadow hover:shadow-md cursor-pointer">
        <CardContent className="px-0">
          <div className="space-y-3 p-5">
            <div className="flex flex-wrap gap-1.5">
              <Badge color="primary" className="text-xs">
                {wod.category.title}
              </Badge>
              <Badge className="text-xs">
                {wod.type.title}
              </Badge>
              {wod.teamSize && wod.teamSize > 1 && (
                <Badge color="secondary" className="text-xs">
                  {t("team")} ×{wod.teamSize}
                </Badge>
              )}
            </div>

            <h3 className="text-xl font-bold leading-snug mb-0">
              {wod.name}
            </h3>
            <p className="text-muted-foreground text-xs tracking-widest uppercase">
              {wod.title}
            </p>

            <p className="text-muted-foreground line-clamp-2 text-sm/5">
              {wod.summary}
            </p>
            <span className="text-primary inline-flex items-center gap-1 text-sm hover:underline">
              {t("view_wod")} <ArrowRight className="size-3.5" />
            </span>
          </div>
        </CardContent>
      </Card>
    </Link>
  )
}
