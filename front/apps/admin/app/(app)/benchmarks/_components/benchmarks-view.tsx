"use client"

import { useTranslations } from "next-intl"
import { useMemo, useState } from "react"
import {
  type BenchmarkScore,
  type Category,
  useGetBenchmarks,
  useGetMyBenchmarkScores,
  CATEGORY,
} from "@workspace/api"

const ALL_CATEGORIES = ["all", ...Object.values(CATEGORY)] as const
type Filter = Category | "all"

import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { PaginationControl } from "@workspace/ui/components/pagination-control"
import { Search } from "lucide-react"
import { useDebounce } from "@workspace/ui/hooks/use-debounce"
import { BenchmarkCard } from "./benchmark-card"

const LIMIT = 9

export function BenchmarksView() {
  const tc = useTranslations("common")

  const [search, setSearch] = useState("")
  const [filter, setFilter] = useState<Filter>("All")
  const [page, setPage] = useState(1)

  const debouncedSearch = useDebounce(search)

  const handleSearchChange = (value: string) => {
    setSearch(value)
    setPage(1)
  }

  const handleFilterChange = (value: Filter) => {
    setFilter(value)
    setPage(1)
  }

  const params: Record<string, string> = {
    page: String(page),
    limit: String(LIMIT),
  }

  if (debouncedSearch) params["filters[name][like]"] = debouncedSearch
  if (filter !== "all") params["filters[category][eq]"] = filter

  const { data: benchmarks, isLoading, error, pagination } =
    useGetBenchmarks(params)

  const { data: myScores } =
    useGetMyBenchmarkScores({ sort: "-performedAt", limit: "200" })

  const scoresByBenchmark = useMemo(() => {
    const map = new Map<string, BenchmarkScore[]>()

    for (const s of myScores ?? []) {
      const list = map.get(s.benchmark.id) ?? []
      list.push(s)
      map.set(s.benchmark.id, list)
    }

    return map
  }, [myScores])

  return (
    <div className="flex flex-col gap-4">

      {/* TOOLBAR */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div className="relative w-full sm:w-64">
          <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground" />

          <Input
            placeholder={tc("benchmarks.search")}
            value={search}
            onChange={(e) => handleSearchChange(e.target.value)}
            className="pl-8 h-8 text-sm"
          />
        </div>

        <div className="flex gap-1 flex-wrap">
          {ALL_CATEGORIES.map((t) => (
            <Button
              key={t}
              size="sm"
              variant={filter === t ? "secondary" : "ghost"}
              className="cursor-pointer px-2.5 text-xs h-7"
              onClick={() => handleFilterChange(t)}
            >
              {tc(`benchmarks.categories.${t}`)}
            </Button>
          ))}
        </div>

      </div>

      {/* LOADING */}
      {isLoading && (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: LIMIT }).map((_, i) => (
            <Skeleton key={i} className="h-44 w-full rounded-xl" />
          ))}
        </div>
      )}

      {/* ERROR */}
      {!isLoading && error && (
        <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">
          Failed to load benchmarks.
        </div>
      )}

      {/* RESULTATS */}
      {!isLoading && !error && benchmarks && (
        benchmarks.length === 0 ? (
          <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">
            No benchmarks found.
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {benchmarks.map((b) => (
                <BenchmarkCard
                  key={b.id}
                  benchmark={b}
                  scores={scoresByBenchmark.get(b.id) ?? []}
                />
              ))}
            </div>

            {pagination && pagination.totalPages > 1 && (
              <div className="mt-4">
                <PaginationControl
                  pagination={pagination}
                  onPageChange={setPage}
                />
              </div>
            )}
          </>
        )
      )}

    </div>
  )
}