"use client"

import { useTranslations } from "next-intl"
import { cn } from "@workspace/ui/lib/utils"
import { Button } from "@workspace/ui/components/button"
import { Field, FieldGroup, FieldLabel } from "@workspace/ui/components/field"
import { Input } from "@workspace/ui/components/input"

interface RegisterFormProps extends Omit<React.ComponentProps<"form">, "onSubmit"> {
  onSubmit?: (data: { firstName: string; lastName: string; email: string; password: string }) => Promise<void>
  isLoading?: boolean
  error?: string
  loginHref?: string
}

export function RegisterForm(
  {
    className,
    onSubmit: onSubmitProp,
    isLoading,
    error,
    loginHref,
    ...props
  }: RegisterFormProps,
) {
  const t = useTranslations("common.register")

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    await onSubmitProp?.({
      firstName: formData.get("firstName") as string,
      lastName: formData.get("lastName") as string,
      email: formData.get("email") as string,
      password: formData.get("password") as string,
    })
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
        <div className="grid grid-cols-2 gap-3">
          <Field>
            <FieldLabel htmlFor="firstName">{t("first_name")}</FieldLabel>
            <Input
              id="firstName"
              name="firstName"
              type="text"
              placeholder="John"
              required
              disabled={isLoading}
              className="bg-background"
            />
          </Field>
          <Field>
            <FieldLabel htmlFor="lastName">{t("last_name")}</FieldLabel>
            <Input
              id="lastName"
              name="lastName"
              type="text"
              placeholder="Doe"
              required
              disabled={isLoading}
              className="bg-background"
            />
          </Field>
        </div>
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
            {isLoading ? t("signing_up") : t("sign_up")}
          </Button>
        </Field>
        {loginHref && (
          <p className="text-center text-sm text-muted-foreground">
            {t("already_account")}{" "}
            <a href={loginHref} className="underline underline-offset-4 hover:text-primary">
              {t("sign_in")}
            </a>
          </p>
        )}
      </FieldGroup>
    </form>
  )
}
