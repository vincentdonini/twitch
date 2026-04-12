"use client"

import {
  AddCoachDrawer,
} from "@/app/(app)/(owner)/owner/[companyId]/places/[placeId]/coaches/_components/add-coach-drawer"
import { useState } from "react"
import {
  type ColumnDef,
  type SortingState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from "@tanstack/react-table"
import { Search, Trash2 } from "lucide-react"

import { apiFetch, useGetPlaceCoaches, User } from "@workspace/api"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@workspace/ui/components/table"

interface CoachesTableProps {
  placeId: string,
  coaches: User[]
  onRemove: () => void
}

function getInitials(firstName: string, lastName: string) {
  return `${firstName[0] ?? ""}${lastName[0] ?? ""}`.toUpperCase()
}

export function CoachesTable({ placeId, coaches, onRemove }: CoachesTableProps) {
  const [sorting, setSorting] = useState<SortingState>([])
  const [globalFilter, setGlobalFilter] = useState("")

  const { refetch } = useGetPlaceCoaches(placeId)

  async function handleRemove(coach: User) {
    try {
      await apiFetch(`/places/${placeId}/coaches/${coach.id}`, { method: "DELETE" })
    } finally {
      refetch()
      onRemove()
    }
  }

  const columns: ColumnDef<User>[] = [
    {
      id: "coach",
      accessorFn: (row) => `${row.firstName} ${row.lastName} ${row.email}`,
      header: "Coach",
      cell: ({ row }) => {
        const { firstName, lastName, email } = row.original
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
      id: "actions",
      header: "",
      cell: ({ row }) => (
        <div className="flex justify-end">
          <Button
            variant="ghost"
            size="icon"
            className="h-8 w-8 cursor-pointer text-destructive hover:text-destructive"
            onClick={() => handleRemove(row.original)}
          >
            <Trash2 className="size-4" />
            <span className="sr-only">Retirer</span>
          </Button>
        </div>
      ),
    },
  ]

  const table = useReactTable({
    data: coaches,
    columns,
    onSortingChange: setSorting,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onGlobalFilterChange: setGlobalFilter,
    state: { sorting, globalFilter },
  })

  return (
    <div className="w-full space-y-4">
      <div className="flex items-center justify-between">
        <div className="relative max-w-sm flex-1">
          <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Rechercher un coach..."
            value={globalFilter ?? ""}
            onChange={(e) => setGlobalFilter(String(e.target.value))}
            className="pl-9"
          />
        </div>
        <div className="flex items-center space-x-2">
          <AddCoachDrawer placeId={placeId} onAdded={() => { refetch(); onRemove() }} />
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
                  Aucun coach associé à cette salle.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>

      <div className="flex items-center justify-between py-2">
        <span className="text-sm text-muted-foreground">
          {table.getFilteredRowModel().rows.length} coach(s)
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
