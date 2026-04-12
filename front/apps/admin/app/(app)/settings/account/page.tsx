"use client"

import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Input } from "@workspace/ui/components/input"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@workspace/ui/components/form"
import { Button } from "@workspace/ui/components/button"
import { Separator } from "@workspace/ui/components/separator"
import { useAuth, useUpdateMe, useUpdatePassword } from "@workspace/api"
import { useEffect } from "react"
import { toast } from "sonner"
import { useTranslations } from "next-intl"

function buildProfileSchema(t: ReturnType<typeof useTranslations<"account">>) {
  return z.object({
    firstName: z.string().min(1, t("val_first_name_required")),
    lastName: z.string().min(1, t("val_last_name_required")),
    email: z.string().email(t("val_email_invalid")),
  })
}

function buildPasswordSchema(t: ReturnType<typeof useTranslations<"account">>) {
  return z
    .object({
      currentPassword: z.string().min(1, t("val_current_password_required")),
      newPassword: z.string().min(8, t("val_new_password_min")),
      confirmPassword: z.string().min(1, t("val_confirm_password_required")),
    })
    .refine((d) => d.newPassword === d.confirmPassword, {
      message: t("val_passwords_mismatch"),
      path: ["confirmPassword"],
    })
}

type ProfileValues = { firstName: string; lastName: string; email: string }
type PasswordValues = { currentPassword: string; newPassword: string; confirmPassword: string }

export default function AccountSettings() {
  const t = useTranslations("account")
  const { user, refreshMe } = useAuth()
  const { mutate: updateMe, isLoading: savingProfile, error: profileError } = useUpdateMe()
  const { mutate: updatePassword, isLoading: savingPassword, error: passwordError } = useUpdatePassword()

  const profileForm = useForm<ProfileValues>({
    resolver: zodResolver(buildProfileSchema(t)),
    defaultValues: { firstName: "", lastName: "", email: "" },
  })

  useEffect(() => {
    if (user) {
      profileForm.reset({ firstName: user.firstName, lastName: user.lastName, email: user.email })
    }
  }, [user]) // eslint-disable-line react-hooks/exhaustive-deps

  async function onProfileSubmit(values: ProfileValues) {
    try {
      await updateMe(values)
      await refreshMe()
      toast.success(t("toast_profile_success"))
    } catch {
      // error shown inline
    }
  }

  const passwordForm = useForm<PasswordValues>({
    resolver: zodResolver(buildPasswordSchema(t)),
    defaultValues: { currentPassword: "", newPassword: "", confirmPassword: "" },
  })

  async function onPasswordSubmit(values: PasswordValues) {
    try {
      await updatePassword({ currentPassword: values.currentPassword, newPassword: values.newPassword })
      passwordForm.reset()
      toast.success(t("toast_password_success"))
    } catch {
      // error shown inline
    }
  }

  return (
    <div className="space-y-6 px-4 lg:px-6">
      <div>
        <h1 className="text-3xl font-bold">{t("page_title")}</h1>
        <p className="text-muted-foreground">{t("page_description")}</p>
      </div>

      {/* ── Personal information ── */}
      <Form {...profileForm}>
        <form onSubmit={profileForm.handleSubmit(onProfileSubmit)}>
          <Card>
            <CardHeader>
              <CardTitle>{t("profile_title")}</CardTitle>
              <CardDescription>{t("profile_description")}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={profileForm.control}
                  name="firstName"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>{t("field_first_name")}</FormLabel>
                      <FormControl>
                        <Input placeholder={t("placeholder_first_name")} {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={profileForm.control}
                  name="lastName"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>{t("field_last_name")}</FormLabel>
                      <FormControl>
                        <Input placeholder={t("placeholder_last_name")} {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
              <FormField
                control={profileForm.control}
                name="email"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>{t("field_email")}</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder={t("placeholder_email")} {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              {profileError && <p className="text-sm text-destructive">{profileError}</p>}
            </CardContent>
          </Card>
          <div className="mt-4 flex gap-2">
            <Button type="submit" disabled={savingProfile} className="cursor-pointer">
              {savingProfile ? t("btn_saving") : t("btn_save")}
            </Button>
            <Button
              type="button"
              variant="outline"
              className="cursor-pointer"
              onClick={() => user && profileForm.reset({ firstName: user.firstName, lastName: user.lastName, email: user.email })}
            >
              {t("btn_cancel")}
            </Button>
          </div>
        </form>
      </Form>

      {/* ── Change password ── */}
      <Form {...passwordForm}>
        <form onSubmit={passwordForm.handleSubmit(onPasswordSubmit)}>
          <Card>
            <CardHeader>
              <CardTitle>{t("password_title")}</CardTitle>
              <CardDescription>{t("password_description")}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <FormField
                control={passwordForm.control}
                name="currentPassword"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>{t("field_current_password")}</FormLabel>
                    <FormControl>
                      <Input type="password" placeholder="••••••••" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={passwordForm.control}
                name="newPassword"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>{t("field_new_password")}</FormLabel>
                    <FormControl>
                      <Input type="password" placeholder="••••••••" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={passwordForm.control}
                name="confirmPassword"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>{t("field_confirm_password")}</FormLabel>
                    <FormControl>
                      <Input type="password" placeholder="••••••••" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              {passwordError && <p className="text-sm text-destructive">{passwordError}</p>}
            </CardContent>
          </Card>
          <div className="mt-4 flex gap-2">
            <Button type="submit" disabled={savingPassword} className="cursor-pointer">
              {savingPassword ? t("btn_updating") : t("btn_update_password")}
            </Button>
            <Button
              type="button"
              variant="outline"
              className="cursor-pointer"
              onClick={() => passwordForm.reset()}
            >
              {t("btn_cancel")}
            </Button>
          </div>
        </form>
      </Form>

      {/* ── Danger zone ── */}
      <Card>
        <CardHeader>
          <CardTitle>{t("danger_title")}</CardTitle>
          <CardDescription>{t("danger_description")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <Separator />
          <div className="flex flex-wrap gap-2 items-center justify-between">
            <div>
              <h4 className="font-semibold">{t("delete_account_title")}</h4>
              <p className="text-sm text-muted-foreground">{t("delete_account_description")}</p>
            </div>
            <Button variant="destructive" type="button" className="cursor-pointer">
              {t("btn_delete_account")}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
