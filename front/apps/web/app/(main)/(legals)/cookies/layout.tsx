import { getTranslations } from "next-intl/server"
import { createMetadata, APP_NAME } from "@/lib/metadata"
import RootLayout from "@/ui/layouts/root-layout"
import { Metadata } from "next"

export async function generateMetadata():Promise<Metadata> {
  const t = await getTranslations("cookies")
  return createMetadata({
    title: `${t("metadata.title")} | ${APP_NAME}`,
    description: t("metadata.description"),
  })
}

export default function CookiesLayout({ children }: { children: React.ReactNode }) {
  return <>{children}</>
}
