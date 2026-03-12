import { getTranslations } from "next-intl/server"
import { env } from "@/lib/env"
import { PageContainer } from "@/ui/components/page-container"

export default async function Page() {
  const t = await getTranslations("privacy")

  const params = {
    company: env.COMPANY_NAME,
    appName: env.APP_NAME,
    minAge: env.MINIMUM_AGE,
    strong: (chunks: React.ReactNode) => <strong>{chunks}</strong>,
  }

  return (
    <PageContainer>

      <h1>{t("title")}</h1>
      <p><em>{t("last_updated", { date: env.LAST_UPDATED })}</em></p>
      <p>{t.rich("intro", params)}</p>
      <p>{t("consent")}</p>

      <h2>{t("s1_title")}</h2>
      <h3>{t("s1_personal_title")}</h3>
      <p>{t("s1_personal_intro")}</p>
      <ul>{(t.raw("s1_personal_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

      <h3>{t("s1_usage_title")}</h3>
      <p>{t("s1_usage_intro")}</p>
      <ul>{(t.raw("s1_usage_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

      <h3>{t("s1_fitness_title")}</h3>
      <p>{t("s1_fitness_body")}</p>

      <h2>{t("s2_title")}</h2>
      <p>{t("s2_intro")}</p>
      <ul>{(t.raw("s2_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

      <h2>{t("s3_title")}</h2>
      <p>{t("s3_intro")}</p>
      <ul>{(t.raw("s3_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>

      <h2>{t("s4_title")}</h2>
      <p>{t("s4_body1")}</p>
      <p>{t("s4_body2")}</p>

      <h2>{t("s5_title")}</h2>
      <p>{t("s5_body")}</p>

      <h2>{t("s6_title")}</h2>
      <p>{t("s6_intro")}</p>
      <ul>{(t.raw("s6_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
      <p>{t("s6_contact")}</p>

      <h2>{t("s7_title")}</h2>
      <p>{t.rich("s7_body1", params)}</p>
      <p>{t("s7_body2")}</p>

      <h2>{t("s8_title")}</h2>
      <p>{t("s8_body1")}</p>
      <p>{t("s8_body2")}</p>

      <h2>{t("s9_title")}</h2>
      <p>{t("s9_intro")}</p>
      <p>
        <strong>{t("s9_email")} :</strong> {env.CONTACT_EMAIL}<br />
        <strong>{t("s9_company")} :</strong> {env.COMPANY_NAME}<br />
        <strong>{t("s9_address")} :</strong> {env.COMPANY_ADDRESS}
      </p>

    </PageContainer>
  )
}
