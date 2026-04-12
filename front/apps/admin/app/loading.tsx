import { getTranslations } from "next-intl/server"

export default async function Loading() {
  const t = await getTranslations("app")

  return (
    <div className="flex min-h-screen items-center justify-center">
      <div className="text-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
        <p className="text-muted-foreground mt-2">{t("loading")}</p>
      </div>
    </div>
  )
}
