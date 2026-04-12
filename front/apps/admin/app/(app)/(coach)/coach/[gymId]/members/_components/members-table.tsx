"use client"

import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@workspace/ui/components/table"
import {
  type ColumnDef,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  type SortingState,
  useReactTable,
  type VisibilityState,
} from "@tanstack/react-table"
import { type Subscription } from "@workspace/api"
import { Search } from "lucide-react"
import { useState } from "react"

interface MembersTableProps {
  gymId: string
  subscriptions: Subscription[]
}

function getInitials(firstName: string, lastName: string) {
  return `${firstName[0] ?? ""}${lastName[0] ?? ""}`.toUpperCase()
}

function formatPrice(cents: number): string {
  return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(cents / 100)
}

function statusVariant(status: string): { variant?: "outline"; color?: "default" | "secondary" | "destructive" } {
  switch (status) {
    case "ACTIVE": return { color: "default" }
    case "PENDING": return { color: "secondary" }
    case "CANCELLED": return { color: "destructive" }
    default: return { variant: "outline" }
  }
}

function statusLabel(status: string): string {
  switch (status) {
    case "ACTIVE":
      return "Active"
    case "PENDING":
      return "En attente"
    case "CANCELLED":
      return "Annulée"
    case "EXPIRED":
      return "Expirée"
    default:
      return status
  }
}

export function MembersTable({ subscriptions }: MembersTableProps) {
  const [sorting, setSorting] = useState<SortingState>([])
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({})
  const [globalFilter, setGlobalFilter] = useState("")

  const columns: ColumnDef<Subscription>[] = [
    {
      id: "member",
      accessorFn: (row) => `${row.user.firstName} ${row.user.lastName} ${row.user.email}`,
      header: "Membre",
      cell: ({ row }) => {
        const { firstName, lastName, email } = row.original.user
        return (
          <div className="flex items-center gap-3">
            <Avatar className="h-8 w-8">
              <AvatarFallback className="text-xs font-medium">
                {getInitials(firstName, lastName)}
              </AvatarFallback>
            </Avatar>
            <div className="flex flex-col">
              <span className="font-medium">{firstName} {lastName}</span>
              <span className="text-sm text-muted-foreground">{email}</span>
            </div>
          </div>
        )
      },
    },
    {
      id: "plan",
      accessorFn: (row) => row.formula.title,
      header: "Formule",
      cell: ({ row }) => (
        <div className="flex flex-col">
          <span className="font-medium">{row.original.formula.title}</span>
          <span className="text-xs text-muted-foreground">{formatPrice(row.original.price)}</span>
        </div>
      ),
    },
    {
      accessorKey: "status",
      header: "Statut",
      cell: ({ row }) => (
        <Badge {...statusVariant(row.original.status)}>
          {statusLabel(row.original.status)}
        </Badge>
      ),
    },
    {
      id: "startedAt",
      accessorFn: (row) => row.startedAt,
      header: "Depuis",
      cell: ({ row }) => (
        <span className="text-sm text-muted-foreground">
          {new Date(row.original.startedAt).toLocaleDateString("fr-FR")}
        </span>
      ),
    },
  ]

  const table = useReactTable({
    data: subscriptions,
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
      <div className="relative max-w-sm">
        <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <Input
          placeholder="Rechercher un membre..."
          value={globalFilter ?? ""}
          onChange={(e) => setGlobalFilter(String(e.target.value))}
          className="pl-9"
        />
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
                  Aucun membre trouvé.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>

      <div className="flex items-center justify-between py-2">
        <div className="flex items-center space-x-2">
          <Label htmlFor="page-size" className="text-sm font-medium">Afficher</Label>
          <Select
            value={`${table.getState().pagination.pageSize}`}
            onValueChange={(v) => table.setPageSize(Number(v))}
          >
            <SelectTrigger className="w-20 cursor-pointer" id="page-size">
              <SelectValue />
            </SelectTrigger>
            <SelectContent side="top">
              {[10, 20, 30, 50].map((s) => (
                <SelectItem key={s} value={`${s}`}>{s}</SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <span className="text-sm text-muted-foreground">
          {table.getFilteredRowModel().rows.length} abonnement(s)
        </span>
        <div className="flex items-center space-x-2">
          <Button variant="outline" size="sm" onClick={() => table.previousPage()}
                  disabled={!table.getCanPreviousPage()} className="cursor-pointer">
            Précédent
          </Button>
          <Button variant="outline" size="sm" onClick={() => table.nextPage()} disabled={!table.getCanNextPage()}
                  className="cursor-pointer">
            Suivant
          </Button>
        </div>
      </div>
    </div>
  )
}
