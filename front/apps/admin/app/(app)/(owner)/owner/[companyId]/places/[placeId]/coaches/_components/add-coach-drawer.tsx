"use client"

import { useEffect, useState } from "react"
import { UserPlus, Search, UserX } from "lucide-react"

import { apiFetch, useSearchUsers, type User } from "@workspace/api"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@workspace/ui/components/sheet"

function getInitials(firstName: string, lastName: string) {
  return `${firstName[0] ?? ""}${lastName[0] ?? ""}`.toUpperCase()
}

interface AddCoachDrawerProps {
  placeId: string
  onAdded: () => void
}

export function AddCoachDrawer({ placeId, onAdded }: AddCoachDrawerProps) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState("")
  const [debouncedQuery, setDebouncedQuery] = useState("")
  const [isAdding, setIsAdding] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const { data: users, isLoading } = useSearchUsers(debouncedQuery)

  useEffect(() => {
    const timer = setTimeout(() => setDebouncedQuery(query), 300)
    return () => clearTimeout(timer)
  }, [query])

  function handleClose() {
    setOpen(false)
    setQuery("")
    setDebouncedQuery("")
    setError(null)
  }

  async function handleAdd(user: User) {
    setIsAdding(true)
    setError(null)
    try {
      await apiFetch(`/places/${placeId}/coaches`, {
        method: "POST",
        body: JSON.stringify({ email: user.email }),
      })
      handleClose()
      onAdded()
    } catch (e) {
      setError(e instanceof Error ? e.message : "Erreur lors de l'ajout.")
    } finally {
      setIsAdding(false)
    }
  }

  const showResults = debouncedQuery.trim().length >= 2
  const noResults = showResults && !isLoading && (!users || users.length === 0)

  return (
    <Sheet open={open} onOpenChange={(v) => { if (!v) handleClose(); else setOpen(true) }}>
      <SheetTrigger asChild>
        <Button className="cursor-pointer">
          <UserPlus className="mr-2 size-4" />
          Ajouter un coach
        </Button>
      </SheetTrigger>
      <SheetContent className="w-full sm:max-w-md">
        <SheetHeader>
          <SheetTitle>Ajouter un coach</SheetTitle>
          <SheetDescription>
            Recherchez un utilisateur existant à associer comme coach.
          </SheetDescription>
        </SheetHeader>

        <div className="mt-6 space-y-4 px-4">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder="Nom, prénom ou email..."
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              className="pl-9"
              autoFocus
            />
          </div>

          {error && <p className="text-sm text-destructive">{error}</p>}

          {showResults && (
            <div className="rounded-md border divide-y">
              {isLoading && (
                <p className="px-4 py-3 text-sm text-muted-foreground">Recherche...</p>
              )}

              {noResults && (
                <div className="flex flex-col items-center gap-2 px-4 py-6 text-muted-foreground">
                  <UserX className="size-8" />
                  <p className="text-sm">Aucun utilisateur trouvé.</p>
                </div>
              )}

              {!isLoading && users && users.map((user) => (
                <div
                  key={user.id}
                  className="flex items-center justify-between px-4 py-3"
                >
                  <div className="flex items-center gap-3">
                    <Avatar className="h-8 w-8">
                      <AvatarFallback className="text-xs font-medium">
                        {getInitials(user.firstName, user.lastName)}
                      </AvatarFallback>
                    </Avatar>
                    <div className="flex flex-col">
                      <span className="text-sm font-medium">{user.firstName} {user.lastName}</span>
                      <span className="text-xs text-muted-foreground">{user.email}</span>
                    </div>
                  </div>
                  <Button
                    size="sm"
                    variant="outline"
                    className="cursor-pointer"
                    disabled={isAdding}
                    onClick={() => handleAdd(user)}
                  >
                    Ajouter
                  </Button>
                </div>
              ))}
            </div>
          )}

          {!showResults && (
            <p className="text-sm text-muted-foreground text-center pt-4">
              Tapez au moins 2 caractères pour rechercher.
            </p>
          )}
        </div>
      </SheetContent>
    </Sheet>
  )
}
