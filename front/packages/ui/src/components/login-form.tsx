"use client"

import { useTranslations } from "next-intl"
import { cn } from "@workspace/ui/lib/utils"
import { Button } from "@workspace/ui/components/button"
import { Field, FieldGroup, FieldLabel } from "@workspace/ui/components/field"
import { Input } from "@workspace/ui/components/input"

interface LoginFormProps extends Omit<React.ComponentProps<"form">, "onSubmit"> {
  onSubmit?: (email: string, password: string) => Promise<void>
  isLoading?: boolean
  error?: string
  registerHref?: string
}

export function LoginForm(
  {
    className,
    onSubmit: onSubmitProp,
    isLoading,
    error,
    registerHref,
    ...props
  }: LoginFormProps,
) {
  const t = useTranslations("common.login")

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    const email = formData.get("email") as string
    const password = formData.get("password") as string
    await onSubmitProp?.(email, password)
  }

  return (
    <form className={cn("flex flex-col gap-6", className)} onSubmit={handleSubmit} {...props}>
      <FieldGroup>
        <div className="flex flex-col items-center gap-1 text-center">
          <h1 className="text-2xl font-bold">{t("title")}</h1>
          <p className="text-sm text-balance text-muted-foreground">
            {t("subtitle")}
          </p>
        </div>
        {error && (
          <p className="text-sm text-destructive text-center">{error}</p>
        )}
        <Field>
          <FieldLabel htmlFor="email">{t("email")}</FieldLabel>
          <Input
            id="email"
            name="email"
            type="email"
            placeholder="m@example.com"
            required
            disabled={isLoading}
            className="bg-background"
          />
        </Field>
        <Field>
          <FieldLabel htmlFor="password">{t("password")}</FieldLabel>
          <Input
            id="password"
            name="password"
            type="password"
            required
            disabled={isLoading}
            className="bg-background"
          />
        </Field>
        <Field>
          <Button type="submit" disabled={isLoading}>
            {isLoading ? t("signing_in") : t("sign_in")}
          </Button>
        </Field>
        {registerHref && (
          <p className="text-center text-sm text-muted-foreground">
            {t("no_account")}{" "}
            <a href={registerHref} className="underline underline-offset-4 hover:text-primary">
              {t("sign_up")}
            </a>
          </p>
        )}
      </FieldGroup>
    </form>
  )
}