"use client"

import { useCallback, useEffect, useState } from "react"
import { useLocale } from "next-intl"
import { apiFetchWithMeta, type PaginationMeta } from "../client"

export interface UseQueryResult<T> {
  data: T | null;
  isLoading: boolean;
  error: string | null;
  pagination: PaginationMeta | null;
  refetch: () => void;
}

export function useQuery<T>(
  url: string,
  params: Record<string, string | string[]> = {},
  auth = true,
): UseQueryResult<T> {
  const locale = useLocale()
  const [data, setData] = useState<T | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [pagination, setPagination] = useState<PaginationMeta | null>(null)
  const [trigger, setTrigger] = useState(0)

  const paramsKey = JSON.stringify(params)

  const run = useCallback(() => {
    setIsLoading(true)
    setError(null)

    const currentParams: Record<string, string | string[]> = JSON.parse(paramsKey)

    apiFetchWithMeta<T>(url, currentParams, auth)
      .then(({ data, pagination }) => {
        setData(data)
        setPagination(pagination)
      })
      .catch((err: unknown) => {
        setError(err instanceof Error ? err.message : "Unknown error")
      })
      .finally(() => setIsLoading(false))
  }, [url, paramsKey, auth, locale])

  useEffect(() => {
    run()
  }, [run, trigger])

  const refetch = useCallback(() => setTrigger((n) => n + 1), [])

  return { data, isLoading, error, pagination, refetch }
}
