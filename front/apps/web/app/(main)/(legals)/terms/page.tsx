import { getTranslations } from "next-intl/server"
import { env } from "@/lib/env"
import { PageContainer } from "@/ui/components/page-container"

export default async function Page() {
  const t = await getTranslations("terms")

  const params = {
    company: env.COMPANY_NAME,
    appName: env.APP_NAME,
    payment: env.PAYMENT_PROVIDER,
    strong: (chunks: React.ReactNode) => <strong>{chunks}</strong>,
  }

  return (
    <PageContainer>

        <h1>{t("title")}</h1>
        <p><em>{t("last_updated", { date: env.LAST_UPDATED })}</em></p>
        <p>{t.rich("intro", params)}</p>

        <h2>{t("s1_title")}</h2>
        <p>{t("s1_body")}</p>
        <p>{t("s1_prohibition")}</p>
        <ul>{(t.raw("s1_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
        <p>{t("s1_termination")}</p>

        <h2>{t("s2_title")}</h2>
        <p>{t("s2_body")}</p>
        <p>{t("s2_agree")}</p>
        <ul>{(t.raw("s2_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
        <p>{t("s2_responsibility")}</p>

        <h2>{t("s3_title")}</h2>
        <p>{t("s3_body1")}</p>
        <p>{t.rich("s3_body2", params)}</p>
        <p>{t("s3_body3")}</p>
        <p>{t.rich("s3_body4", params)}</p>

        <h2>{t("s4_title")}</h2>
        <p>{t.rich("s4_body1", params)}</p>
        <p>{t("s4_body2")}</p>

        <h2>{t("s5_title")}</h2>
        <p>{t.rich("s5_body1", params)}</p>
        <p>{t("s5_body2")}</p>
        <ul>
          <li>{t("s5_payment_item", { payment: env.PAYMENT_PROVIDER })}</li>
          {(t.raw("s5_items") as string[]).map((item) => <li key={item}>{item}</li>)}
        </ul>

        <h2>{t("s6_title")}</h2>
        <p>{t.rich("s6_intro", params)}</p>
        <ul>{(t.raw("s6_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
        <p>{t("s6_risk")}</p>

        <h2>{t("s7_title")}</h2>
        <p>{t("s7_intro")}</p>
        <ul>{(t.raw("s7_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
        <p>{t("s7_stop")}</p>

        <h2>{t("s8_title")}</h2>
        <p>{t("s8_body1")}</p>
        <p>{t("s8_body2")}</p>
        <ul>{(t.raw("s8_items") as string[]).map((item) => <li key={item}>{item}</li>)}</ul>
        <p>{t("s8_body3")}</p>

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
