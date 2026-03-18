import { DM_Sans, Geist_Mono, Lato } from "next/font/google"
import { AuthProvider } from "@workspace/api"
import "@workspace/ui/globals.css"
import { ThemeProvider } from "@workspace/ui/components/theme-provider"
import { cn } from "@workspace/ui/lib/utils"
import { NextIntlClientProvider } from "next-intl"
import { getLocale } from "@/i18n/request"
import React from "react"

const dmSans = DM_Sans({ subsets: ["latin"], variable: "--font-sans" })

const fontMono = Geist_Mono({
  subsets: ["latin"],
  variable: "--font-mono",
})

export default async function RootLayout(
  {
    children,
  }: Readonly<{ children: React.ReactNode }>,
) {
  const locale = await getLocale()
  const appMessages = (await import(`@/messages/${locale}.json`)).default
  const uiMessages = (await import(`@workspace/ui/messages/${locale}.json`)).default
  const messages = { ...appMessages, ui: uiMessages }

  return (
    <html
      lang={locale}
      suppressHydrationWarning
      className={cn("antialiased", fontMono.variable, "font-sans", dmSans.variable)}
    >
      <body>
        <NextIntlClientProvider locale={locale} messages={messages}>
          <ThemeProvider>
            <AuthProvider>
              {children}
            </AuthProvider>
          </ThemeProvider>
        </NextIntlClientProvider>
      </body>
    </html>
  )
}
