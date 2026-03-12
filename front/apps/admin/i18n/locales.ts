export const locales = ["en", "fr"] as const
export type Locale = (typeof locales)[number]

const envLocale = process.env.NEXT_PUBLIC_DEFAULT_LOCALE
export const defaultLocale: Locale =
  envLocale && locales.includes(envLocale as Locale) ? (envLocale as Locale) : "en"
