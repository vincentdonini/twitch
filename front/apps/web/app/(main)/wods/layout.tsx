import { APP_NAME, createMetadata } from "@/lib/metadata"
import { Metadata } from "next"
import { getTranslations } from "next-intl/server"

export async function generateMetadata(): Promise<Metadata> {
  const t = await getTranslations("wods")
  return createMetadata({
    title: `${t("title")} | ${APP_NAME}`,
    description: t("description"),
  })
}

export default function WodLayout({ children }: { children: React.ReactNode }) {
  return <>{children}</>
}
