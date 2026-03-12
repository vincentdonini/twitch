import { getTranslations } from "next-intl/server"
import { createMetadata, APP_NAME } from "@/lib/metadata"

export async function generateMetadata() {
  const t = await getTranslations("muscles")
  return createMetadata({
    title: `${t("title")} | ${APP_NAME}`,
    description: t("description"),
  })
}

export default function MuscleDetailLayout({ children }: { children: React.ReactNode }) {
  return children
}
