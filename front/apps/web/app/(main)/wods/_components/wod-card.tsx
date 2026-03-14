"use client"

import Link from "next/link"
import { useTranslations } from "next-intl"
import { ArrowRight } from "lucide-react"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent } from "@workspace/ui/components/card"
import type { Wod } from "@workspace/api"

export function WodCard({ wod }: { wod: Wod }) {
  const t = useTranslations("wods")
  return (
    <Link href={`/wods/${wod.id}`}>
      <Card className="h-full overflow-hidden py-0 transition-shadow hover:shadow-md cursor-pointer">
        <CardContent className="px-0">
          <div className="space-y-3 p-5">
            <div className="flex flex-wrap gap-1.5">
              <Badge variant="outline" className="text-xs">
                {wod.category.title}
              </Badge>
              <Badge variant="secondary" className="text-xs">
                {wod.type.title}
              </Badge>
              {wod.teamSize && wod.teamSize > 1 && (
                <Badge variant="secondary" className="text-xs">
                  Team ×{wod.teamSize}
                </Badge>
              )}
            </div>
            <p className="text-muted-foreground font-mono text-xs tracking-widest uppercase">
              {wod.name}
            </p>
            <h3 className="text-base font-bold leading-snug">
              {wod.title}
            </h3>
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
