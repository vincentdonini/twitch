import { getTranslations } from "next-intl/server"
import { env } from "@/lib/env"
import { PageContainer } from "@/ui/components/page-container"

export default async function Page() {
  const t = await getTranslations("cookies")

  const params = {
    company: env.COMPANY_NAME,
    appName: env.APP_NAME,
    strong: (chunks: React.ReactNode) => <strong>{chunks}</strong>,
  }

  return (
    <PageContainer>

        <h1>{t("title")}</h1>
        <p><em>{t("last_updated", { date: env.LAST_UPDATED })}</em></p>
        <p>{t.rich("intro", params)}</p>
        <p>{t("consent")}</p>

        <h2>{t("s1_title")}</h2>
        <p>{t("s1_body")}</p>

        <h2>{t("s2_title")}</h2>
        <p>{t("s2_intro")}</p>
        <ul>{(t.raw("s2_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

        <h2>{t("s3_title")}</h2>
        <h3>{t("s3_essential_title")}</h3>
        <p>{t("s3_essential_body")}</p>
        <h3>{t("s3_analytics_title")}</h3>
        <p>{t("s3_analytics_body")}</p>
        <h3>{t("s3_preference_title")}</h3>
        <p>{t("s3_preference_body")}</p>
        <h3>{t("s3_third_title")}</h3>
        <p>{t("s3_third_body")}</p>

        <h2>{t("s4_title")}</h2>
        <p>{t("s4_body1")}</p>
        <p>{t("s4_body2")}</p>
        <ul>{(t.raw("s4_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

        <h2>{t("s5_title")}</h2>
        <p>{t("s5_body1")}</p>
        <p>{t("s5_body2")}</p>

        <h2>{t("s6_title")}</h2>
        <p>{t("s6_intro")}</p>
        <p>
          <strong>{t("s6_email")} :</strong> {env.CONTACT_EMAIL}<br />
          <strong>{t("s6_company")} :</strong> {env.COMPANY_NAME}<br />
          <strong>{t("s6_address")} :</strong> {env.COMPANY_ADDRESS}
        </p>

    </PageContainer>
  )
}
