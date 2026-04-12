"use client"

import { Button } from "@workspace/ui/components/button"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@workspace/ui/components/form"
import { Input } from "@workspace/ui/components/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from "@workspace/ui/components/sheet"
import { Switch } from "@workspace/ui/components/switch"
import { Textarea } from "@workspace/ui/components/textarea"
import { zodResolver } from "@hookform/resolvers/zod"

import { useCreatePlan } from "@workspace/api"
import { Plus } from "lucide-react"
import { useTranslations } from "next-intl"
import { useState } from "react"
import { useForm } from "react-hook-form"
import { z } from "zod"

function buildSchema(t: ReturnType<typeof useTranslations<"plans">>) {
  return z
    .object({
      title: z.string().min(2, t("val_title_min")),
      description: z.string().optional(),
      type: z.enum(["SUBSCRIPTION", "PACK", "DROP_IN"]),
      price: z.coerce.number().min(0, t("val_price_min")),
      status: z.enum(["ACTIVE", "INACTIVE", "ARCHIVED"]),
      isPublic: z.boolean(),
      // SUBSCRIPTION
      billingPeriod: z.enum(["WEEKLY", "MONTHLY", "YEARLY"]).optional(),
      engagementDurationInMonths: z.coerce.number().int().min(0).optional(),
      // PACK
      totalSessions: z.coerce.number().int().min(2, t("val_sessions_min")).optional(),
    })
    .superRefine((data, ctx) => {
      if (data.type === "SUBSCRIPTION") {
        if (!data.billingPeriod) {
          ctx.addIssue({ code: z.ZodIssueCode.custom, message: t("val_required"), path: ["billingPeriod"] })
        }
        if (data.engagementDurationInMonths === undefined || data.engagementDurationInMonths === null) {
          ctx.addIssue({ code: z.ZodIssueCode.custom, message: t("val_required"), path: ["engagementDurationInMonths"] })
        }
      }
      if (data.type === "PACK") {
        if (!data.totalSessions || data.totalSessions < 2) {
          ctx.addIssue({ code: z.ZodIssueCode.custom, message: t("val_sessions_min"), path: ["totalSessions"] })
        }
      }
    })
}

type FormValues = {
  title: string
  description?: string
  type: "SUBSCRIPTION" | "PACK" | "DROP_IN"
  price: number
  status: "ACTIVE" | "INACTIVE" | "ARCHIVED"
  isPublic: boolean
  billingPeriod?: "WEEKLY" | "MONTHLY" | "YEARLY"
  engagementDurationInMonths?: number
  totalSessions?: number
}

interface CreatePlanDrawerProps {
  placeId: string
  onCreated: () => void
}

export function CreatePlanDrawer({ placeId, onCreated }: CreatePlanDrawerProps) {
  const t = useTranslations("plans")
  const [open, setOpen] = useState(false)
  const { mutate, isLoading, error } = useCreatePlan(placeId)

  const form = useForm<FormValues>({
    resolver: zodResolver(buildSchema(t)),
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
          {t("create_button")}
        </Button>
      </SheetTrigger>
      <SheetContent className="w-full sm:max-w-md overflow-y-auto">
        <SheetHeader>
          <SheetTitle>{t("create_title")}</SheetTitle>
          <SheetDescription>{t("create_description")}</SheetDescription>
        </SheetHeader>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="mt-6 space-y-4 px-4">
            <FormField
              control={form.control}
              name="title"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>{t("field_title")}</FormLabel>
                  <FormControl>
                    <Input placeholder={t("field_title_placeholder")} {...field} />
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
                    {t("field_description")}{" "}
                    <span className="text-muted-foreground text-xs">{t("field_description_optional")}</span>
                  </FormLabel>
                  <FormControl>
                    <Textarea placeholder={t("field_description_placeholder")} rows={3} {...field} />
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
                    <FormLabel>{t("field_type")}</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="SUBSCRIPTION">{t("type_subscription")}</SelectItem>
                        <SelectItem value="PACK">{t("type_pack")}</SelectItem>
                        <SelectItem value="DROP_IN">{t("type_drop_in")}</SelectItem>
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
                    <FormLabel>{t("field_status")}</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="ACTIVE">{t("status_active")}</SelectItem>
                        <SelectItem value="INACTIVE">{t("status_inactive")}</SelectItem>
                        <SelectItem value="ARCHIVED">{t("status_archived")}</SelectItem>
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
                  <FormLabel>{t("field_price")}</FormLabel>
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
                      <FormLabel>{t("field_billing_period")}</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="WEEKLY">{t("field_period_weekly")}</SelectItem>
                          <SelectItem value="MONTHLY">{t("field_period_monthly")}</SelectItem>
                          <SelectItem value="YEARLY">{t("field_period_yearly")}</SelectItem>
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
                      <FormLabel>{t("field_engagement")}</FormLabel>
                      <FormControl>
                        <Input type="number" min="0" step="1" placeholder={t("field_engagement_placeholder")} {...field} />
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
                    <FormLabel>{t("field_sessions")}</FormLabel>
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
                    <FormLabel className="text-sm font-medium">{t("field_is_public")}</FormLabel>
                    <p className="text-xs text-muted-foreground">{t("field_is_public_hint")}</p>
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
                {t("btn_cancel")}
              </Button>
              <Button type="submit" disabled={isLoading} className="cursor-pointer">
                {isLoading ? t("btn_creating") : t("btn_create")}
              </Button>
            </div>
          </form>
        </Form>
      </SheetContent>
    </Sheet>
  )
}
