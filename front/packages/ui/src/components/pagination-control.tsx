"use client"

import { useTranslations } from "next-intl"
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "./pagination"

export interface PaginationMeta {
  total: number;
  page: number;
  totalPages: number;
  limit: number;
}

interface PaginationControlProps {
  pagination: PaginationMeta;
  onPageChange: (page: number) => void;
  siblings?: number;
}

function buildPageRange(
  current: number,
  total: number,
  siblings: number
): (number | "…")[] {

  const pages: (number | "…")[] = []
  const delta = siblings

  const left = Math.max(2, current - delta)
  const right = Math.min(total - 1, current + delta)

  pages.push(1)

  if (left > 2) {
    pages.push("…")
  }

  for (let i = left; i <= right; i++) {
    pages.push(i)
  }

  if (right < total - 1) {
    pages.push("…")
  }

  if (total > 1) {
    pages.push(total)
  }

  return pages
}

export function PaginationControl({ pagination, onPageChange, siblings = 1 }: PaginationControlProps) {
  const t = useTranslations("ui.pagination")
  const { page, totalPages } = pagination

  if (totalPages <= 1) return null

  const pages = buildPageRange(page, totalPages, siblings)

  return (
    <Pagination>
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            href="#"
            text={t("previous")}
            aria-disabled={page <= 1}
            className={page <= 1 ? "pointer-events-none opacity-50" : ""}
            onClick={(e) => {
              e.preventDefault()
              if (page > 1) onPageChange(page - 1)
            }}
          />
        </PaginationItem>

        {pages.map((p, i) =>
          p === "…" ? (
            <PaginationItem key={`ellipsis-${i}`}>
              <PaginationEllipsis />
            </PaginationItem>
          ) : (
            <PaginationItem key={p}>
              <PaginationLink
                href="#"
                isActive={p === page}
                onClick={(e) => {
                  e.preventDefault()
                  if (p !== page) onPageChange(p)
                }}
              >
                {p}
              </PaginationLink>
            </PaginationItem>
          ),
        )}

        <PaginationItem>
          <PaginationNext
            href="#"
            text={t("next")}
            aria-disabled={page >= totalPages}
            className={page >= totalPages ? "pointer-events-none opacity-50" : ""}
            onClick={(e) => {
              e.preventDefault()
              if (page < totalPages) onPageChange(page + 1)
            }}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  )
}
