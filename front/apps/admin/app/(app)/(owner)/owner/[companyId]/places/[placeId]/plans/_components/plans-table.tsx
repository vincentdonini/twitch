"use client"

import {
  CreatePlanDrawer,
} from "@/app/(app)/(owner)/owner/[companyId]/places/[placeId]/plans/_components/create-plan-drawer"
import { PlanDetailsDrawer } from "@/app/(app)/(owner)/owner/[companyId]/places/[placeId]/plans/_components/plan-details-drawer"
import { useState } from "react"
import {
  type ColumnDef,
  type SortingState,
  type VisibilityState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from "@tanstack/react-table"
import { Search, Trash2 } from "lucide-react"
import { useTranslations } from "next-intl"

import { Plan, useGetPlacePlans } from "@workspace/api"
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

interface PlansTableProps {
  placeId: string,
  plans: Plan[]
  onDelete: (plan: Plan) => void
  onCreated: () => void
  onUpdated: () => void
}

function formatPrice(cents: number): string {
  return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(cents / 100)
}

function statusVariant(status: string): { variant?: "outline"; color?: "default" | "secondary" | "destructive" } {
  switch (status) {
    case "ACTIVE": return { color: "default" }
    case "INACTIVE": return { color: "secondary" }
    case "ARCHIVED": return { color: "destructive" }
    default: return { variant: "outline" }
  }
}

export function PlansTable({ placeId, plans, onDelete, onCreated, onUpdated }: PlansTableProps) {
  const t = useTranslations("plans")
  const [sorting, setSorting] = useState<SortingState>([])
  const [globalFilter, setGlobalFilter] = useState("")
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({})

  const { refetch } = useGetPlacePlans(placeId)

  const columns: ColumnDef<Plan>[] = [
    {
      accessorKey: "title",
      header: t("col_title"),
      cell: ({ row }) => (
        <div className="flex flex-col">
          <span className="font-medium">{row.original.title}</span>
          {row.original.description && (
            <span className="text-xs text-muted-foreground line-clamp-1">{row.original.description}</span>
          )}
        </div>
      ),
    },
    {
      accessorKey: "type",
      header: t("col_type"),
      cell: ({ row }) => {
        const label = {
          SUBSCRIPTION: t("type_subscription"),
          PACK: t("type_pack"),
          DROP_IN: t("type_drop_in"),
        }[row.original.type] ?? row.original.type
        return <Badge variant="outline">{label}</Badge>
      },
    },
    {
      accessorKey: "price",
      header: t("col_price"),
      cell: ({ row }) => (
        <span className="font-medium">{formatPrice(row.original.price)}</span>
      ),
    },
    {
      accessorKey: "billingPeriod",
      header: t("col_billing_period"),
      cell: ({ row }) => {
        const period = row.original.billingPeriod
        const label = period
          ? ({ WEEKLY: t("period_weekly"), MONTHLY: t("period_monthly"), YEARLY: t("period_yearly") }[period] ?? period)
          : t("period_none")
        return <span className="text-sm">{label}</span>
      },
    },
    {
      accessorKey: "status",
      header: t("col_status"),
      cell: ({ row }) => {
        const label = {
          ACTIVE: t("status_active"),
          INACTIVE: t("status_inactive"),
          ARCHIVED: t("status_archived"),
        }[row.original.status] ?? row.original.status
        return <Badge {...statusVariant(row.original.status)}>{label}</Badge>
      },
    },
    {
      accessorKey: "isPublic",
      header: t("col_visibility"),
      cell: ({ row }) => (
        <span className="text-sm text-muted-foreground">
          {row.original.isPublic ? t("visibility_public") : t("visibility_private")}
        </span>
      ),
    },
    {
      id: "actions",
      header: "",
      cell: ({ row }) => (
        <div className="flex items-center justify-end gap-1">
          <PlanDetailsDrawer
            placeId={placeId}
            plan={row.original}
            onUpdated={() => { refetch(); onUpdated() }}
          />
          <Button
            variant="ghost"
            size="icon"
            className="h-8 w-8 cursor-pointer text-destructive hover:text-destructive"
            onClick={() => onDelete(row.original)}
          >
            <Trash2 className="size-4" />
            <span className="sr-only">Supprimer</span>
          </Button>
        </div>
      ),
    },
  ]

  const table = useReactTable({
    data: plans,
    columns,
    onSortingChange: setSorting,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onGlobalFilterChange: setGlobalFilter,
    state: { sorting, columnVisibility, globalFilter },
  })

  return (
    <div className="w-full space-y-4">
      <div className="flex items-center justify-between">
        <div className="relative max-w-sm flex-1">
          <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder={t("search_placeholder")}
            value={globalFilter ?? ""}
            onChange={(e) => setGlobalFilter(String(e.target.value))}
            className="pl-9"
          />
        </div>
        <div className="flex items-center space-x-2">
          <CreatePlanDrawer placeId={placeId} onCreated={() => { refetch(); onCreated() }} />
        </div>
      </div>

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
            {table.getRowModel().rows?.length ? (
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
                <TableCell colSpan={columns.length} className="h-24 text-center">
                  {t("empty")}
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>

      <div className="flex items-center justify-between py-2">
        <div className="flex items-center space-x-2">
          <Label htmlFor="page-size" className="text-sm">{t("show")}</Label>
          <Select
            value={`${table.getState().pagination.pageSize}`}
            onValueChange={(v) => table.setPageSize(Number(v))}
          >
            <SelectTrigger className="w-20 cursor-pointer" id="page-size">
              <SelectValue />
            </SelectTrigger>
            <SelectContent side="top">
              {[10, 20, 50].map((s) => (
                <SelectItem key={s} value={`${s}`}>{s}</SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <span className="text-sm text-muted-foreground">
          {t("count", { count: table.getFilteredRowModel().rows.length })}
        </span>
        <div className="flex items-center space-x-2">
          <Button variant="outline" size="sm" onClick={() => table.previousPage()}
                  disabled={!table.getCanPreviousPage()} className="cursor-pointer">
            {t("previous")}
          </Button>
          <Button variant="outline" size="sm" onClick={() => table.nextPage()} disabled={!table.getCanNextPage()}
                  className="cursor-pointer">
            {t("next")}
          </Button>
        </div>
      </div>
    </div>
  )
}
