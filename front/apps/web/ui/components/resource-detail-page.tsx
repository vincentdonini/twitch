"use client"

import Link from "next/link"
import { ArrowLeft, ArrowRight, CheckCircle2Icon } from "lucide-react"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Separator } from "@workspace/ui/components/separator"
import { Card, CardContent } from "@workspace/ui/components/card"
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from "@workspace/ui/components/empty"
import ReactMarkdown from "react-markdown"
import { useTranslations } from "next-intl"

interface RelatedItem {
  id: string
  slug: string
  title: string
  summary: string
  href: string
}

export interface RelatedSection {
  heading: string
  items: RelatedItem[]
}

interface ResourceDetailItem {
  slug: string
  title: string
  details: string
}

interface ResourceDetailPageProps {
  item: ResourceDetailItem | null
  isLoading: boolean
  error: string | null
  onRetry: () => void
  href: string
  backLabel: string
  badgeLabel: string
  errorTitle: string
  errorDescription: string
  relatedSections?: RelatedSection[]
}

export function ResourceDetailPage(
  {
    item,
    isLoading,
    error,
    onRetry,
    href,
    backLabel,
    badgeLabel,
    errorTitle,
    errorDescription,
    relatedSections,
  }: ResourceDetailPageProps,
) {

  const tc = useTranslations("common")

  const sections = relatedSections?.filter((s) => s.items.length > 0) ?? []
  const isSingleSection = sections.length === 1

  return (
    <section className="py-12 sm:py-24 bg-muted/50 min-h-[calc(100vh-65px)]">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">

        <Button variant="ghost" className="mb-8 -ml-2" asChild>
          <Link href={href}>
            <ArrowLeft className="size-4 mr-2" />
            {backLabel}
          </Link>
        </Button>

        {isLoading && (
          <div className="space-y-4 max-w-3xl">
            <Skeleton className="h-6 w-24" />
            <Skeleton className="h-10 w-64" />
            <Skeleton className="h-5 w-full" />
            <Skeleton className="h-5 w-5/6" />
            <Skeleton className="h-40 w-full" />
          </div>
        )}

        {error && (
          <div className="mx-auto max-w-2xl text-center">
            <Card>
              <CardContent className="px-0">
                <Empty>
                  <EmptyHeader>
                    <EmptyMedia variant="icon">
                      <CheckCircle2Icon />
                    </EmptyMedia>
                    <EmptyTitle>
                      {errorTitle}
                    </EmptyTitle>
                    <EmptyDescription>
                      {errorDescription}
                    </EmptyDescription>
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

        {!isLoading && !error && item && (
          <div className="space-y-4 max-w-3xl">
            <p className="text-muted-foreground text-xs tracking-widest uppercase mb-4">
              {item.slug}
            </p>

            <h1>{item.title}</h1>

            <Badge variant="outline" className="mb-8">
              {badgeLabel}
            </Badge>

            <div>
              {item.details && (
                <ReactMarkdown>
                  {item.details}
                </ReactMarkdown>
              )}

              {sections.length > 0 && (
                <>
                  <Separator className="mt-16 mb-10" />
                  <div className={`grid ${isSingleSection ? "grid-cols-1" : "grid-cols-1 md:grid-cols-2"} gap-8 items-stretch`}>

                    {sections.map((section) => (
                      <div key={section.heading} className="flex flex-col gap-4">
                        <h3 className="uppercase">{section.heading}</h3>

                        {section.items.map((related) => (
                          <Link key={related.id} href={related.href} className="flex-1">
                            <Card className="overflow-hidden py-0 hover:shadow-md transition-shadow cursor-pointer h-full">
                              <CardContent className="px-0 h-full">
                                <div className="flex flex-col justify-between h-full space-y-3 p-6">
                                  <div className="space-y-3">
                                    <p className="text-muted-foreground text-xs tracking-widest uppercase">
                                      {related.slug}
                                    </p>
                                    <h3 className="text-xl font-bold hover:text-primary transition-colors">
                                      {related.title}
                                    </h3>
                                    <p className="text-muted-foreground">
                                      {related.summary}
                                    </p>
                                  </div>
                                  <span className="inline-flex items-center gap-2 text-primary hover:underline">
                                    {tc("learn_more")}
                                    <ArrowRight className="size-4" />
                                  </span>
                                </div>
                              </CardContent>
                            </Card>
                          </Link>
                        ))}
                      </div>
                    ))}

                  </div>
                </>
              )}

            </div>
          </div>
        )}

      </div>
    </section>
  )
}