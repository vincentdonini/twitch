"use client"

import { type Wod } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent } from "@workspace/ui/components/card"
import { ArrowRight } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"
import ReactMarkdown from "react-markdown"

interface WodCardProps {
  wod: Wod
}

export function WodCard({ wod }: WodCardProps) {
  const t = useTranslations("common.wods")

  return (
    <Link href={`/wods/${wod.id}`}>
      <Card className="h-full overflow-hidden transition-shadow hover:shadow-md cursor-pointer">
        <CardContent>
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
            {wod.title && wod.title !== wod.name && (
              <div className="text-muted-foreground text-xs tracking-widest uppercase">
                {wod.title}
              </div>
            )}
            <p className="text-muted-foreground line-clamp-2 text-sm/5">
                <ReactMarkdown>
                  {wod.summary}
                </ReactMarkdown>
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
