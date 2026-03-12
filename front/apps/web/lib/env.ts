function requireEnv(name: string, value: string | undefined): string {
  if (!value) throw new Error(`Missing required environment variable: ${name}`)
  return value
}

function optionalEnv(value: string | undefined, fallback: string): string {
  return value ?? fallback
}

export const env = {
  ADMIN_URL: requireEnv("NEXT_PUBLIC_ADMIN_URL", process.env.NEXT_PUBLIC_ADMIN_URL),
  API_URL: requireEnv("NEXT_PUBLIC_API_URL", process.env.NEXT_PUBLIC_API_URL),
  API_LOCALE: optionalEnv(process.env.NEXT_PUBLIC_API_LOCALE, "fr"),
  APP_NAME: requireEnv("NEXT_PUBLIC_APP_NAME", process.env.NEXT_PUBLIC_APP_NAME),
  APP_URL: requireEnv("NEXT_PUBLIC_APP_URL", process.env.NEXT_PUBLIC_APP_URL),
  DEFAULT_LOCALE: optionalEnv(process.env.NEXT_PUBLIC_DEFAULT_LOCALE, "fr"),
  COOKIE_DOMAIN: optionalEnv(process.env.NEXT_PUBLIC_COOKIE_DOMAIN, ""),
  COMPANY_NAME: optionalEnv(process.env.NEXT_PUBLIC_COMPANY_NAME, ""),
  CONTACT_EMAIL: optionalEnv(process.env.NEXT_PUBLIC_CONTACT_EMAIL, ""),
  COMPANY_ADDRESS: optionalEnv(process.env.NEXT_PUBLIC_COMPANY_ADDRESS, ""),
  MINIMUM_AGE: optionalEnv(process.env.NEXT_PUBLIC_MINIMUM_AGE, "16"),
  LAST_UPDATED: optionalEnv(process.env.NEXT_PUBLIC_LAST_UPDATED, ""),
  PAYMENT_PROVIDER: optionalEnv(process.env.NEXT_PUBLIC_PAYMENT_PROVIDER, ""),
}
