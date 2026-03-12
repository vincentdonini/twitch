import { getRequestConfig } from "next-intl/server"
import { cookies } from "next/headers"
import { locales, defaultLocale, type Locale } from "./locales"
import uiMessages from "@workspace/ui/messages/en.json"

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
  const uiLocaleMessages = (await import(`@workspace/ui/messages/${locale}.json`)).default

  return {
    locale,
    messages: { ...appMessages, ui: uiLocaleMessages },
  }
})
