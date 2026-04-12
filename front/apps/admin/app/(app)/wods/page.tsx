import { getTranslations } from "next-intl/server"
import { WodsView } from "./_components/wods-view"

export default async function Page() {
  const t = await getTranslations("wods")

  return (
    <div className="@container/main flex flex-col gap-4 px-4 lg:px-6">
      <div>
        <h1 className="text-2xl font-bold tracking-tight">{t("page_title")}</h1>
        <p className="text-muted-foreground mt-1">{t("page_description")}</p>
      </div>

      <WodsView />
    </div>
  )
}
