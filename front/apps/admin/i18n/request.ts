import { getRequestConfig } from "next-intl/server"
import { cookies } from "next/headers"
import { defaultLocale, type Locale, locales } from "./locales"

export { locales, defaultLocale, type Locale }

export async function getLocale(): Promise<Locale> {
  const cookieStore = await cookies()
  const cookieLocale = cookieStore.get("locale")?.value
  return cookieLocale && locales.includes(cookieLocale as Locale)
    ? (cookieLocale as Locale)
    : defaultLocale
}

export default getRequestConfig(async () => {
  const locale = await getLocale()
  const appMessages = (await import(`../messages/${locale}.json`)).default
  const commonMessages = (await import(`@workspace/ui/messages/${locale}.json`)).default

  return {
    locale,
    messages: { ...appMessages, common: commonMessages},
  }
})
