"use client"

import { Button } from "@workspace/ui/components/button"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@workspace/ui/components/form"
import { Input } from "@workspace/ui/components/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from "@workspace/ui/components/sheet"
import { zodResolver } from "@hookform/resolvers/zod"
import { useCreateSubscription, useCreateUser, useGetPlacePlans } from "@workspace/api"
import { Plus } from "lucide-react"
import { useState } from "react"
import { useForm } from "react-hook-form"
import { z } from "zod"

const schema = z.object({
  firstName: z.string().min(2, "Minimum 2 caractères."),
  lastName: z.string().min(2, "Minimum 2 caractères."),
  email: z.string().email("Adresse email invalide."),
  password: z.string().min(8, "Minimum 8 caractères."),
  planId: z.string().min(1, "Sélectionnez une formule."),
})

type FormValues = z.infer<typeof schema>

interface CreateMemberDrawerProps {
  gymId: string
  onCreated: () => void
}

function formatPrice(cents: number): string {
  return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(cents / 100)
}

export function CreateMemberDrawer({ gymId, onCreated }: CreateMemberDrawerProps) {
  const [open, setOpen] = useState(false)
  const [globalError, setGlobalError] = useState<string | null>(null)

  const createUser = useCreateUser()
  const { data: plans = [] } = useGetPlacePlans(gymId)
  const activePlans = (plans ?? []).filter((f) => f.status === "ACTIVE")

  const form = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { firstName: "", lastName: "", email: "", password: "", planId: "" },
  })

  const selectedPlanId = form.watch("planId")
  const createSubscription = useCreateSubscription(gymId, selectedPlanId)

  async function onSubmit(values: FormValues) {
    setGlobalError(null)
    try {
      // 1. Créer le compte
      const { id: userId } = await createUser.mutate({
        firstName: values.firstName,
        lastName: values.lastName,
        email: values.email,
        password: values.password,
      })

      // 2. Créer la subscription
      await createSubscription.mutate({ userId })

      form.reset()
      setOpen(false)
      onCreated()
    } catch (e) {
      setGlobalError(
        createUser.error ?? createSubscription.error ?? "Une erreur est survenue.",
      )
    }
  }

  const isLoading = createUser.isLoading || createSubscription.isLoading

  return (
    <Sheet open={open} onOpenChange={(v) => {
      setOpen(v)
      if (!v) {
        form.reset()
        setGlobalError(null)
      }
    }}>
      <SheetTrigger asChild>
        <Button className="cursor-pointer">
          <Plus className="mr-2 size-4" />
          Créer un membre
        </Button>
      </SheetTrigger>
      <SheetContent className="w-full sm:max-w-md overflow-y-auto">
        <SheetHeader>
          <SheetTitle>Créer un membre</SheetTitle>
          <SheetDescription>
            Créez un compte et assignez une formule à ce nouvel athlète.
          </SheetDescription>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="mt-6 space-y-4 px-4">
            <div className="grid grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="firstName"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Prénom</FormLabel>
                    <FormControl><Input placeholder="Jean" {...field} /></FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="lastName"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nom</FormLabel>
                    <FormControl><Input placeholder="Dupont" {...field} /></FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <FormField
              control={form.control}
              name="email"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Email</FormLabel>
                  <FormControl>
                    <Input type="email" placeholder="jean.dupont@email.com" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="password"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Mot de passe</FormLabel>
                  <FormControl>
                    <Input type="password" placeholder="••••••••" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="planId"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Formule</FormLabel>
                  <Select onValueChange={field.onChange} defaultValue={field.value}>
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Sélectionnez une formule" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      {activePlans.length === 0 && (
                        <SelectItem value="__none" disabled>
                          Aucune formule active disponible
                        </SelectItem>
                      )}
                      {activePlans.map((f) => (
                        <SelectItem key={f.id} value={f.id}>
                          {f.title} — {formatPrice(f.price)}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            {globalError && <p className="text-sm text-destructive">{globalError}</p>}

            <div className="flex justify-end gap-2 pt-4">
              <Button
                type="button"
                variant="outline"
                className="cursor-pointer"
                onClick={() => setOpen(false)}
              >
                Annuler
              </Button>
              <Button type="submit" disabled={isLoading || activePlans.length === 0} className="cursor-pointer">
                {isLoading ? "Création..." : "Créer"}
              </Button>
            </div>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  )
}
