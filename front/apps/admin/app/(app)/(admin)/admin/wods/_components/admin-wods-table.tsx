"use client"

import { useEffect, useRef, useState } from "react"
import {
  type ColumnDef,
  flexRender,
  getCoreRowModel,
  getPaginationRowModel,
  useReactTable,
} from "@tanstack/react-table"
import { Plus, Search, Settings2 } from "lucide-react"
import {
  useGetWods,
  useGetWodTypes,
  useGetWodCategories,
  type Wod,
} from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@workspace/ui/components/select"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@workspace/ui/components/table"
import { useTranslations } from "next-intl"
import { WodSheet } from "./wod-sheet"

export function AdminWodsTable() {
  const t = useTranslations("admin_wods")

  const [search, setSearch] = useState("")
  const [debouncedSearch, setDebouncedSearch] = useState("")
  const [typeId, setTypeId] = useState("")
  const [categoryId, setCategoryId] = useState("")
  const [teamFilter, setTeamFilter] = useState("")

  const [sheetOpen, setSheetOpen] = useState(false)
  const [editWod, setEditWod] = useState<Wod | null>(null)

  // Debounce search input
  const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null)
  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current)
    debounceRef.current = setTimeout(() => setDebouncedSearch(search), 400)
    return () => { if (debounceRef.current) clearTimeout(debounceRef.current) }
  }, [search])

  // Build API params
  const params: Record<string, string> = {}
  if (debouncedSearch) params["filters[name][like]"] = debouncedSearch
  if (typeId) params["filters[type.id][eq]"] = typeId
  if (categoryId) params["filters[category.id][eq]"] = categoryId
  if (teamFilter === "team") params["filters[teamSize][gt]"] = "0"

  const { data, isLoading, error, refetch } = useGetWods(params)
  const { data: wodTypes } = useGetWodTypes()
  const { data: wodCategories } = useGetWodCategories()

  const wods = data ?? []

  function handleNew() {
    setEditWod(null)
    setSheetOpen(true)
  }

  function handleEdit(wod: Wod) {
    setEditWod(wod)
    setSheetOpen(true)
  }

  function handleSheetSuccess() {
    setSheetOpen(false)
    refetch()
  }

  function clearFilters() {
    setTypeId("")
    setCategoryId("")
    setTeamFilter("")
  }

  const hasActiveFilters = !!(typeId || categoryId || teamFilter)

  const columns: ColumnDef<Wod>[] = [
    {
      id: "name",
      accessorKey: "name",
      header: t("col_name"),
      cell: ({ row }) => (
        <span className="font-medium">{row.original.name}</span>
      ),
    },
    {
      id: "type",
      accessorFn: (row) => row.type.title,
      header: t("col_type"),
      cell: ({ row }) => (
        <Badge color="secondary" className="font-normal">
          {row.original.type.title}
        </Badge>
      ),
    },
    {
      id: "category",
      accessorFn: (row) => row.category.title,
      header: t("col_category"),
      cell: ({ row }) => (
        <span className="text-sm text-muted-foreground">{row.original.category.title}</span>
      ),
    },
    {
      id: "variants",
      accessorFn: (row) => row.variants.length,
      header: t("col_variants"),
      cell: ({ row }) => (
        <span className="text-sm">{row.original.variants.length}</span>
      ),
    },
    {
      id: "team",
      header: t("col_team"),
      cell: ({ row }) => (
        <span className="text-sm text-muted-foreground">
          {row.original.teamSize ?? t("team_individual")}
        </span>
      ),
    },
    {
      id: "actions",
      header: "",
      cell: ({ row }) => (
        <div className="flex justify-end">
          <Button
            variant="ghost"
            size="icon"
            className="h-8 w-8 cursor-pointer"
            onClick={() => handleEdit(row.original)}
          >
            <Settings2 className="size-4" />
            <span className="sr-only">Edit</span>
          </Button>
        </div>
      ),
    },
  ]

  const table = useReactTable({
    data: wods,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
  })

  if (error) {
    return (
      <div className="flex items-center justify-center py-16 text-destructive">
        {t("error")}
      </div>
    )
  }

  return (
    <>
      <div className="w-full space-y-4">
        {/* ── Toolbar ── */}
        <div className="flex items-center gap-3">
          <div className="relative flex-1 max-w-sm">
            <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder={t("search_placeholder")}
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="pl-9"
            />
          </div>
          <Button onClick={handleNew} className="cursor-pointer ml-auto">
            <Plus className="size-4 mr-2" />
            {t("create_button")}
          </Button>
        </div>

        {/* ── Filters ── */}
        <div className="grid gap-2 sm:grid-cols-3 sm:gap-4">
          <div className="space-y-1.5">
            <Label className="text-sm font-medium">{t("col_type")}</Label>
            <Select
              value={typeId}
              onValueChange={(v) => setTypeId(v === "all" ? "" : v)}
            >
              <SelectTrigger>
                <SelectValue placeholder={t("filter_all_types")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">{t("filter_all_types")}</SelectItem>
                {(wodTypes ?? []).map((opt) => (
                  <SelectItem key={opt.id} value={opt.id}>{opt.title}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-1.5">
            <Label className="text-sm font-medium">{t("col_category")}</Label>
            <Select
              value={categoryId}
              onValueChange={(v) => setCategoryId(v === "all" ? "" : v)}
            >
              <SelectTrigger>
                <SelectValue placeholder={t("filter_all_categories")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">{t("filter_all_categories")}</SelectItem>
                {(wodCategories ?? []).map((opt) => (
                  <SelectItem key={opt.id} value={opt.id}>{opt.title}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-1.5">
            <Label className="text-sm font-medium">{t("col_team")}</Label>
            <Select
              value={teamFilter}
              onValueChange={(v) => setTeamFilter(v === "all" ? "" : v)}
            >
              <SelectTrigger>
                <SelectValue placeholder={t("filter_all_teams")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">{t("filter_all_teams")}</SelectItem>
                <SelectItem value="team">{t("filter_team_only")}</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {hasActiveFilters && (
            <div className="flex items-center gap-2">
              <Button
                variant="ghost"
                size="sm"
                className="h-7 px-2 text-xs cursor-pointer"
                onClick={clearFilters}
              >
                {t("filter_clear")}
              </Button>
            </div>
          )}
        </div>


        {/* ── Table ── */}
        <div className="rounded-md border">
          <Table>
            <TableHeader>
              {table.getHeaderGroups().map((headerGroup) => (
                <TableRow key={headerGroup.id}>
                  {headerGroup.headers.map((header) => (
                    <TableHead key={header.id}>
                      {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                    </TableHead>
                  ))}
                </TableRow>
              ))}
            </TableHeader>
            <TableBody>
              {isLoading ? (
                <TableRow>
                  <TableCell colSpan={columns.length} className="h-24 text-center text-muted-foreground">
                    {t("loading")}
                  </TableCell>
                </TableRow>
              ) : table.getRowModel().rows?.length ? (
                table.getRowModel().rows.map((row) => (
                  <TableRow key={row.id}>
                    {row.getVisibleCells().map((cell) => (
                      <TableCell key={cell.id}>
                        {flexRender(cell.column.columnDef.cell, cell.getContext())}
                      </TableCell>
                    ))}
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={columns.length} className="h-24 text-center text-muted-foreground">
                    {t("empty")}
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>

        {/* ── Pagination ── */}
        <div className="flex items-center justify-between py-2">
          <span className="text-sm text-muted-foreground">
            {wods.length} WOD(s)
          </span>
          <div className="flex items-center space-x-2">
            <Button
              variant="outline"
              size="sm"
              onClick={() => table.previousPage()}
              disabled={!table.getCanPreviousPage()}
              className="cursor-pointer"
            >
              {t("previous")}
            </Button>
            <Button
              variant="outline"
              size="sm"
              onClick={() => table.nextPage()}
              disabled={!table.getCanNextPage()}
              className="cursor-pointer"
            >
              {t("next")}
            </Button>
          </div>
        </div>
      </div>

      <WodSheet
        mode={editWod ? "edit" : "create"}
        wod={editWod ?? undefined}
        open={sheetOpen}
        onOpenChange={setSheetOpen}
        onSuccess={handleSheetSuccess}
      />
    </>
  )
}
