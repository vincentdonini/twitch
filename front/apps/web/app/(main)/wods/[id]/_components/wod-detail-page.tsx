"use client"

import Link from "next/link"
import { useTranslations } from "next-intl"
import { AlertCircleIcon, ArrowLeft } from "lucide-react"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Card, CardContent } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import {
  Empty, EmptyContent, EmptyDescription,
  EmptyHeader, EmptyMedia, EmptyTitle,
} from "@workspace/ui/components/empty"
import type { WodDetail } from "@workspace/api"
import { WodDetailSkeleton } from "./wod-detail-skeleton"
import { MarkdownSection } from "./markdown-section"
import { VariantCard } from "./variant-card"

interface WodDetailPageProps {
  item: WodDetail | null
  isLoading: boolean
  error: string | null
  onRetry: () => void
}

export function WodDetailPage({ item, isLoading, error, onRetry }: WodDetailPageProps) {
  const t  = useTranslations("wods")
  const tc = useTranslations("common")

  return (
    <section className="py-12 sm:py-24 bg-muted/50 min-h-[calc(100vh-65px)]">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">

        <Button variant="ghost" className="mb-8 -ml-2" asChild>
          <Link href="/wods">
            <ArrowLeft className="size-4 mr-2" />
            {t("back")}
          </Link>
        </Button>

        {isLoading && <WodDetailSkeleton />}

        {error && (
          <Card>
            <CardContent className="px-0">
              <Empty>
                <EmptyHeader>
                  <EmptyMedia variant="icon"><AlertCircleIcon /></EmptyMedia>
                  <EmptyTitle>{t("error_title")}</EmptyTitle>
                  <EmptyDescription>{t("error_description")}</EmptyDescription>
                </EmptyHeader>
                <EmptyContent>
                  <Button onClick={onRetry}>{tc("retry")}</Button>
                </EmptyContent>
              </Empty>
            </CardContent>
          </Card>
        )}

        {!isLoading && !error && item && (
          <div className="space-y-10">

            {/* Header */}
            <div className="space-y-3">
              <p className="text-muted-foreground font-mono text-xs tracking-widest uppercase">
                {item.name}
              </p>
              <h1 className="text-4xl font-bold tracking-tight">{item.title}</h1>
              <div className="flex flex-wrap gap-2 pt-1">
                <Badge variant="outline">{item.category.title}</Badge>
                <Badge variant="secondary">{item.type.title}</Badge>
                {item.teamSize && item.teamSize > 1 && (
                  <Badge variant="secondary">Team ×{item.teamSize}</Badge>
                )}
              </div>
              {item.summary && (
                <p className="text-muted-foreground text-lg leading-relaxed pt-2">
                  {item.summary}
                </p>
              )}
            </div>

            {/* Details / Rules / Tips */}
            {(item.details || item.rules || item.tips) && (
              <div className="space-y-8">
                {item.details && <MarkdownSection title={t("details")} content={item.details} />}
                {item.rules   && <MarkdownSection title={t("rules")}   content={item.rules} />}
                {item.tips    && <MarkdownSection title={t("tips")}    content={item.tips} />}
              </div>
            )}

            {/* Variants */}
            {item.variants.length > 0 && (
              <div className="space-y-4">
                <Separator />
                <h2 className="text-2xl font-bold">{t("variants")}</h2>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                  {item.variants.map(variant => (
                    <VariantCard key={variant.id} variant={variant} />
                  ))}
                </div>
              </div>
            )}

          </div>
        )}

      </div>
    </section>
  )
}
