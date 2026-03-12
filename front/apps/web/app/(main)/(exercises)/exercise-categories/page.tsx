"use client"

import { useState } from "react"
import { useGetExercises } from "@workspace/api"
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
import {
    PaginationControl,
} from "@workspace/ui/components/pagination-control"

const LIMIT = 9

export default function Page() {
    const [page, setPage] = useState(1)

    const {
        data: exercises,
        isLoading: isLoadingExercises,
        error: errorExercises,
        pagination,
        refetch: refetchExercises,
    } = useGetExercises({page: String(page), limit: String(LIMIT)})

    return (
      <section id="blog" className="py-12 sm:py-24 bg-muted/50 min-vh-100 min-h-[calc(100vh-65px)]">
          <div className="container mx-auto px-4 sm:px-6 lg:px-8">

              {/* Section Header */}
              <div className="mx-auto max-w-2xl text-center mb-16">
                  <Badge variant="outline" className="mb-4">
                      Dictionnary
                  </Badge>

                  <h2 className="text-3xl font-bold tracking-tight sm:text-4xl mb-4">
                      Exercises
                  </h2>

                  <p className="text-lg text-muted-foreground">
                      Discover a variety of exercises, understand how to perform them correctly, and improve your strength, mobility, and overall performance.
                  </p>
              </div>

              {/* Loading */}
              {isLoadingExercises && (
                <Skeleton className="h-[calc(50vh-65px)] w-full" />
              )}

              {/* Error */}
              {errorExercises && (
                <div className="mx-auto max-w-2xl text-center mb-16">
                    <Card>
                        <CardContent className="px-0">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <CheckCircle2Icon />
                                    </EmptyMedia>
                                    <EmptyTitle>Failed to load exercises</EmptyTitle>
                                    <EmptyDescription>We couldn't load the exercise data. Please try again.</EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button onClick={() => refetchExercises()}>
                                        Retry
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>
                </div>
              )}

              {/* Success */}
              {!isLoadingExercises && !errorExercises && (
                <>
                    {/* Grid */}
                    <div className="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
                        {exercises?.map((exercise) => (
                          <Card key={exercise.id} className="overflow-hidden py-0">
                              <CardContent className="px-0">
                                  <div className="space-y-3 p-6">
                                      <p className="text-muted-foreground text-xs tracking-widest uppercase">
                                          {exercise.slug}
                                      </p>

                                      <h3 className="text-xl font-bold hover:text-primary transition-colors">
                                          {exercise.title}
                                      </h3>

                                      <p className="text-muted-foreground">
                                          {exercise.summary}
                                      </p>

                                      <span className="inline-flex items-center gap-2 text-primary hover:underline cursor-pointer">
                      Learn More
                      <ArrowRight className="size-4" />
                    </span>
                                  </div>
                              </CardContent>
                          </Card>
                        ))}
                    </div>

                    {pagination && (
                      <PaginationControl pagination={pagination} onPageChange={setPage}/>
                    )}
                </>
              )}

          </div>
      </section>
    )
}
