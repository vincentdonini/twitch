import { getTranslations } from "next-intl/server"
import { createMetadata, APP_NAME } from "@/lib/metadata"
import { Metadata } from "next"

export async function generateMetadata():Promise<Metadata> {
  const t = await getTranslations("exercises")
  return createMetadata({
    title: `${t("title")} | ${APP_NAME}`,
    description: t("description"),
  })
}

export default function ExerciseLayout({ children }: { children: React.ReactNode }) {
  return <>{children}</>
}
