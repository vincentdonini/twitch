"use client"

import Link from "next/link"
import { ArrowRight, CheckCircle2Icon } from "lucide-react"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Card, CardContent } from "@workspace/ui/components/card"
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from "@workspace/ui/components/empty"
import { PaginationControl, type PaginationMeta } from "@workspace/ui/components/pagination-control"
import { useTranslations } from "next-intl"
import { PageContainer } from "@/ui/components/page-container"

interface ResourceItem {
  id: string
  slug: string
  title: string
  summary: string
}

interface ResourceListPageProps {
  data: ResourceItem[] | null
  isLoading: boolean
  error: string | null
  pagination: PaginationMeta | null
  onPageChange: (page: number) => void
  onRetry: () => void
  getHref?: (id: string) => string
  title: string
  description: string
  errorTitle: string
  errorDescription: string
}

export function ResourceListPage(
  {
    data,
    isLoading,
    error,
    pagination,
    onPageChange,
    onRetry,
    getHref,
    title,
    description,
    errorTitle,
    errorDescription,
  }: ResourceListPageProps,
) {
  const tc = useTranslations("common")

  return (
    <PageContainer size="wide" withPadding={true}>

      <div className="mx-auto max-w-2xl text-center mb-16">
        <Badge variant="outline" className="mb-4">
          {tc("badge_resources")}
        </Badge>
        <h2 className="text-3xl font-bold tracking-tight sm:text-4xl mb-4">
          {title}
        </h2>
        <p className="text-lg text-muted-foreground">
          {description}
        </p>
      </div>

      {isLoading && (
        <Skeleton className="h-[calc(50vh-65px)] w-full" />
      )}

      {error && (
        <div className="mx-auto max-w-2xl text-center mb-16">
          <Card>
            <CardContent className="px-0">
              <Empty>
                <EmptyHeader>
                  <EmptyMedia variant="icon"><CheckCircle2Icon /></EmptyMedia>
                  <EmptyTitle>{errorTitle}</EmptyTitle>
                  <EmptyDescription>{errorDescription}</EmptyDescription>
                </EmptyHeader>
                <EmptyContent>
                  <Button onClick={onRetry}>
                    {tc("retry")}
                  </Button>
                </EmptyContent>
              </Empty>
            </CardContent>
          </Card>
        </div>
      )}

      {!isLoading && !error && (
        <>
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8 items-stretch">
            {data?.map((item) => {
              const card = (
                <Card className="overflow-hidden py-0 hover:shadow-md transition-shadow cursor-pointer h-full">
                  <CardContent className="px-0">
                    <div className="space-y-3 p-6">
                      <p className="text-muted-foreground text-xs tracking-widest uppercase">
                        {item.slug}
                      </p>
                      <h3 className="text-xl font-bold hover:text-primary transition-colors">
                        {item.title}
                      </h3>
                      <p className="text-muted-foreground">
                        {item.summary}
                      </p>
                      <span className="inline-flex items-center gap-2 text-primary hover:underline">
                          {tc("learn_more")}
                        <ArrowRight className="size-4" />
                        </span>
                    </div>
                  </CardContent>
                </Card>
              )

              return getHref ? (
                <Link key={item.id} href={getHref(item.id)} className="h-full">{card}</Link>
              ) : (
                <div key={item.id} className="h-full">{card}</div>
              )
            })}
          </div>

          {pagination && (
            <PaginationControl pagination={pagination} onPageChange={onPageChange} />
          )}
        </>
      )}

    </PageContainer>
  )
}
