"use client"

import { MarkdownSection } from "@/app/(main)/wods/[id]/_components/markdown-section"
import { WodDetailSkeleton } from "@/app/(main)/wods/[id]/_components/wod-detail-skeleton"
import { WodSidebar } from "@/app/(main)/wods/[id]/_components/wod-sidebar"
import { PageContainer } from "@/ui/components/page-container"
import { type WodDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Card, CardContent } from "@workspace/ui/components/card"
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from "@workspace/ui/components/empty"
import { Separator } from "@workspace/ui/components/separator"
import { WodPattern } from "@workspace/ui/components/wod-pattern"
import { AlertCircleIcon } from "lucide-react"
import { useTranslations } from "next-intl"

interface WodDetailPageProps {
  item: WodDetail | null
  isLoading: boolean
  error: string | null
  onRetry: () => void
}

export function WodDetailPage(
  {
    item,
    isLoading,
    error,
    onRetry,
  }: WodDetailPageProps,
) {
  const t = useTranslations("wods")
  const tc = useTranslations("common")

  return (
    <PageContainer size="wide" withPadding={true}>
      {isLoading && <WodDetailSkeleton />}
      {!isLoading && <div className="grid grid-cols-1 lg:grid-cols-6 lg:gap-6">
        <div id="sidebar" className="order-2 lg:order-1 col-span-2">
          {item && <WodSidebar wod={item} />}
        </div>
        <div id="wod-grid" className=" order-1 lg:order-2 col-span-1 md:col-span-4">
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
                <h1 className="text-4xl font-black">
                  {item.name}
                </h1>
                <div className="flex flex-wrap gap-2 pt-1">
                  <Badge color="primary">{item.category.title}</Badge>
                  <Badge color="secondary">{item.type.title}</Badge>
                  {item.teamSize && item.teamSize > 1 && (
                    <Badge color="secondary">Team ×{item.teamSize}</Badge>
                  )}
                </div>
                {item.summary && (
                  <div className="text-muted-foreground text-lg px-2 hidden md:block">
                    <MarkdownSection content={item.summary} />
                  </div>
                )}
              </div>

              {/* Pattern */}
              {item.variants.length > 0 && (
                <WodPattern variants={item.variants} />
              )}

              {/* Details / Rules / Tips */}
              {(item.details || item.rules || item.tips) && (
                <div className="space-y-8">
                  <Separator />
                  {item.details && <MarkdownSection title={t("details")} content={item.details} />}
                  {item.rules && <MarkdownSection title={t("rules")} content={item.rules} />}
                  {item.tips && <MarkdownSection title={t("tips")} content={item.tips} />}
                </div>
              )}

            </div>
          )}
        </div>
      </div>}
    </PageContainer>
  )
}
