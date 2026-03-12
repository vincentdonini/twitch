import { getRequestConfig } from "next-intl/server"
import { cookies } from "next/headers"
import { locales, defaultLocale, type Locale } from "./locales"

export { locales, defaultLocale, type Locale }

export async function getLocale(): Promise<Locale> {
  const cookieStore = await cookies()
  const cookieLocale = cookieStore.get("locale")?.value
  return cookieLocale && locales.includes(cookieLocale as Locale)
    ? (cookieLocale as Locale)
    : defaultLocale
}

export default getRequestConfig(
  async () => {
    const locale = await getLocale()

    return {
      locale,
      messages: (await import(`../messages/${locale}.json`)).default,
    }
  })
