import { getTranslations } from "next-intl/server"
import { createMetadata, APP_NAME } from "@/lib/metadata"
import { Metadata } from "next"

export async function generateMetadata():Promise<Metadata> {
  const t = await getTranslations("muscles")
  return createMetadata({
    title: `${t("metadata.title")} | ${APP_NAME}`,
    description: t("metadata.description"),
  })
}

export default function MuscleLayout({ children }: { children: React.ReactNode }) {
  return <>{children}</>
}
