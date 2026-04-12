import { getTranslations } from "next-intl/server"
import { PlansView } from "./_components/plans-view"
import { StatCards } from "./_components/stat-cards"

interface PageProps {
  params: Promise<{ placeId: string }>
}

export default async function Page({ params }: PageProps) {
  const { placeId } = await params
  const t = await getTranslations("plans")

  return (
    <div className="@container/main flex flex-col gap-4 px-4 lg:px-6">
      <div>
        <h1 className="text-2xl font-bold tracking-tight">{t("page_title")}</h1>
        <p className="text-muted-foreground mt-1">{t("page_description")}</p>
      </div>

      <StatCards />

      <PlansView placeId={placeId} />
    </div>
  )
}
