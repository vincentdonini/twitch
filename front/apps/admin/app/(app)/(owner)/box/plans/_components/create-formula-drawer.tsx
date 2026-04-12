"use client"

import { useState } from "react"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { zodResolver } from "@hookform/resolvers/zod"
import { Plus } from "lucide-react"
import { useCreatePlan } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@workspace/ui/components/sheet"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@workspace/ui/components/form"
import { Input } from "@workspace/ui/components/input"
import { Textarea } from "@workspace/ui/components/textarea"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@workspace/ui/components/select"
import { Switch } from "@workspace/ui/components/switch"

const schema = z
  .object({
    title: z.string().min(2, "Minimum 2 caractères."),
    description: z.string().optional(),
    type: z.enum(["SUBSCRIPTION", "PACK", "DROP_IN"]),
    price: z.coerce.number().min(0, "Le prix doit être positif."),
    status: z.enum(["ACTIVE", "INACTIVE", "ARCHIVED"]),
    isPublic: z.boolean(),
    // SUBSCRIPTION
    billingPeriod: z.enum(["WEEKLY", "MONTHLY", "YEARLY"]).optional(),
    engagementDurationInMonths: z.coerce.number().int().min(0).optional(),
    // PACK
    totalSessions: z.coerce.number().int().min(2, "Minimum 2 séances.").optional(),
  })
  .superRefine((data, ctx) => {
    if (data.type === "SUBSCRIPTION") {
      if (!data.billingPeriod) {
        ctx.addIssue({ code: z.ZodIssueCode.custom, message: "Champ requis.", path: ["billingPeriod"] })
      }
      if (data.engagementDurationInMonths === undefined || data.engagementDurationInMonths === null) {
        ctx.addIssue({ code: z.ZodIssueCode.custom, message: "Champ requis.", path: ["engagementDurationInMonths"] })
      }
    }
    if (data.type === "PACK") {
      if (!data.totalSessions || data.totalSessions < 2) {
        ctx.addIssue({ code: z.ZodIssueCode.custom, message: "Minimum 2 séances.", path: ["totalSessions"] })
      }
    }
  })

type FormValues = z.infer<typeof schema>

interface CreatePlanDrawerProps {
  placeId: string
  onCreated: () => void
}

export function CreatePlanDrawer({ placeId, onCreated }: CreatePlanDrawerProps) {
  const [open, setOpen] = useState(false)
  const { mutate, isLoading, error } = useCreatePlan(placeId)

  const form = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: {
      title: "",
      description: "",
      type: "SUBSCRIPTION",
      price: 0,
      status: "ACTIVE",
      isPublic: true,
      billingPeriod: "MONTHLY",
      engagementDurationInMonths: 1,
      totalSessions: undefined,
    },
  })

  const watchType = form.watch("type")

  async function onSubmit(values: FormValues) {
    try {
      await mutate({
        title: values.title,
        description: values.description || null,
        type: values.type,
        price: Math.round(values.price * 100),
        currency: "EUR",
        status: values.status,
        isPublic: values.isPublic,
        ...(values.type === "SUBSCRIPTION" && {
          billingPeriod: values.billingPeriod,
          engagementDurationInMonths: values.engagementDurationInMonths ?? 0,
        }),
        ...(values.type === "PACK" && {
          totalSessions: values.totalSessions,
        }),
      })
      form.reset()
      setOpen(false)
      onCreated()
    } catch {
      // error already set by the hook
    }
  }

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button className="cursor-pointer">
          <Plus className="mr-2 size-4" />
          Nouvelle formule
        </Button>
      </SheetTrigger>
      <SheetContent className="w-full sm:max-w-md overflow-y-auto">
        <SheetHeader>
          <SheetTitle>Nouvelle formule</SheetTitle>
          <SheetDescription>
            Créez une formule d&apos;abonnement ou un pack pour votre salle.
          </SheetDescription>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="mt-6 space-y-4 px-4">
            <FormField
              control={form.control}
              name="title"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Titre</FormLabel>
                  <FormControl>
                    <Input placeholder="Ex : Abonnement mensuel" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="description"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>
                    Description{" "}
                    <span className="text-muted-foreground text-xs">(optionnel)</span>
                  </FormLabel>
                  <FormControl>
                    <Textarea placeholder="Décrivez la formule..." rows={3} {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <div className="grid grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="type"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Type</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="SUBSCRIPTION">Abonnement</SelectItem>
                        <SelectItem value="PACK">Pack</SelectItem>
                        <SelectItem value="DROP_IN">À la séance</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="status"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Statut</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="ACTIVE">Active</SelectItem>
                        <SelectItem value="INACTIVE">Inactive</SelectItem>
                        <SelectItem value="ARCHIVED">Archivée</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <FormField
              control={form.control}
              name="price"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Prix (€)</FormLabel>
                  <FormControl>
                    <Input type="number" min="0" step="0.01" placeholder="29.99" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            {/* Champs SUBSCRIPTION */}
            {watchType === "SUBSCRIPTION" && (
              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="billingPeriod"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Périodicité</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="WEEKLY">Hebdomadaire</SelectItem>
                          <SelectItem value="MONTHLY">Mensuel</SelectItem>
                          <SelectItem value="YEARLY">Annuel</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="engagementDurationInMonths"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Engagement (mois)</FormLabel>
                      <FormControl>
                        <Input type="number" min="0" step="1" placeholder="0 = sans engagement" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            )}

            {/* Champs PACK */}
            {watchType === "PACK" && (
              <FormField
                control={form.control}
                name="totalSessions"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nombre de séances</FormLabel>
                    <FormControl>
                      <Input type="number" min="2" step="1" placeholder="10" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            )}

            <FormField
              control={form.control}
              name="isPublic"
              render={({ field }) => (
                <FormItem className="flex items-center justify-between rounded-lg border p-3">
                  <div>
                    <FormLabel className="text-sm font-medium">Formule publique</FormLabel>
                    <p className="text-xs text-muted-foreground">Visible par les athlètes</p>
                  </div>
                  <FormControl>
                    <Switch checked={field.value} onCheckedChange={field.onChange} />
                  </FormControl>
                </FormItem>
              )}
            />

            {error && <p className="text-sm text-destructive">{error}</p>}

            <div className="flex justify-end gap-2 pt-4">
              <Button
                type="button"
                variant="outline"
                className="cursor-pointer"
                onClick={() => setOpen(false)}
              >
                Annuler
              </Button>
              <Button type="submit" disabled={isLoading} className="cursor-pointer">
                {isLoading ? "Création..." : "Créer"}
              </Button>
            </div>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  )
}
