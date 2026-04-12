"use client"

import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@workspace/ui/components/form"
import { Input } from "@workspace/ui/components/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from "@workspace/ui/components/sheet"
import { Switch } from "@workspace/ui/components/switch"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { Textarea } from "@workspace/ui/components/textarea"
import { zodResolver } from "@hookform/resolvers/zod"
import {
  type Plan,
  type SubscriptionStatus,
  useGetPlanSubscriptions,
  useUpdatePlan,
} from "@workspace/api"
import { Settings2 } from "lucide-react"
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
      billingPeriod: z.enum(["WEEKLY", "MONTHLY", "YEARLY"]).optional(),
      engagementDurationInMonths: z.coerce.number().int().min(0).optional(),
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

function subStatusVariant(status: SubscriptionStatus): { variant?: "outline"; color?: "default" | "secondary" | "destructive" } {
  switch (status) {
    case "ACTIVE": return { color: "default" }
    case "PENDING": return { color: "secondary" }
    case "CANCELLED": return { color: "destructive" }
    case "EXPIRED": return { variant: "outline" }
  }
}

interface PlanDetailsDrawerProps {
  placeId: string
  plan: Plan
  onUpdated: () => void
}

export function PlanDetailsDrawer({ placeId, plan, onUpdated }: PlanDetailsDrawerProps) {
  const t = useTranslations("plans")
  const [open, setOpen] = useState(false)
  const { mutate, isLoading, error } = useUpdatePlan(placeId, plan.id)
  const { data: subscriptions, isLoading: subsLoading, error: subsError } = useGetPlanSubscriptions(placeId, plan.id, open)

  const form = useForm<FormValues>({
    resolver: zodResolver(buildSchema(t)),
    defaultValues: {
      title: plan.title,
      description: plan.description ?? "",
      type: plan.type,
      price: plan.price / 100,
      status: plan.status,
      isPublic: plan.isPublic,
      billingPeriod: plan.billingPeriod ?? undefined,
      engagementDurationInMonths: plan.engagementDurationInMonths ?? 0,
      totalSessions: plan.totalSessions ?? undefined,
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
      setOpen(false)
      onUpdated()
    } catch {
      // error already set by hook
    }
  }

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button variant="ghost" size="icon" className="h-8 w-8 cursor-pointer">
          <Settings2 className="size-4" />
          <span className="sr-only">{t("edit_button")}</span>
        </Button>
      </SheetTrigger>
      <SheetContent className="w-full sm:max-w-md overflow-y-auto">
        <SheetHeader>
          <SheetTitle>{t("edit_title")}</SheetTitle>
          <SheetDescription>{t("edit_description")}</SheetDescription>
        </SheetHeader>

        <Tabs defaultValue="info" className="mt-6">
          <TabsList className="w-auto mx-4">
            <TabsTrigger value="info" className="flex-1">{t("tab_info")}</TabsTrigger>
            <TabsTrigger value="members" className="flex-1">{t("tab_members")}</TabsTrigger>
          </TabsList>

          {/* ── Information tab ── */}
          <TabsContent value="info">
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="mt-4 space-y-4 px-4">
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
                    {isLoading ? t("btn_saving") : t("btn_save")}
                  </Button>
                </div>
              </form>
            </Form>
          </TabsContent>

          {/* ── Members tab ── */}
          <TabsContent value="members" className="mt-4 px-4">
            {subsLoading && (
              <p className="py-8 text-center text-sm text-muted-foreground">{t("members_loading")}</p>
            )}
            {subsError && (
              <p className="py-8 text-center text-sm text-destructive">{t("members_error")}</p>
            )}
            {!subsLoading && !subsError && subscriptions?.length === 0 && (
              <p className="py-8 text-center text-sm text-muted-foreground">{t("members_empty")}</p>
            )}
            {!subsLoading && !subsError && subscriptions && subscriptions.length > 0 && (
              <ul className="space-y-3">
                {subscriptions.map((sub) => {
                  const initials = [sub.user.firstName, sub.user.lastName]
                    .filter(Boolean)
                    .map((n) => n![0])
                    .join("")
                    .toUpperCase()
                  const statusLabel = ({
                    ACTIVE: t("sub_status_active"),
                    PENDING: t("sub_status_pending"),
                    CANCELLED: t("sub_status_cancelled"),
                    EXPIRED: t("sub_status_expired"),
                  } as Record<string, string>)[sub.status] ?? sub.status

                  return (
                    <li key={sub.id} className="flex items-center gap-3 rounded-lg border p-3">
                      <Avatar className="size-9 shrink-0">
                        <AvatarFallback className="text-xs">{initials || "?"}</AvatarFallback>
                      </Avatar>
                      <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-medium">
                          {sub.user.firstName} {sub.user.lastName}
                        </p>
                        <p className="truncate text-xs text-muted-foreground">{sub.user.email}</p>
                      </div>
                      <div className="flex flex-col items-end gap-1 shrink-0">
                        <Badge {...subStatusVariant(sub.status)} className="text-xs">
                          {statusLabel}
                        </Badge>
                        <span className="text-xs text-muted-foreground">
                          {new Date(sub.startedAt).toLocaleDateString()}
                        </span>
                      </div>
                    </li>
                  )
                })}
              </ul>
            )}
          </TabsContent>
        </Tabs>
      </SheetContent>
    </Sheet>
  )
}
