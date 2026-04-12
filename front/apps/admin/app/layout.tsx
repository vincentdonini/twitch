import type { Metadata } from "next";
import "@workspace/ui/globals.css";
import "./globals.css";

import { Toaster } from "@workspace/ui/components/sonner";
import { ThemeProvider } from "@workspace/ui/components/theme-provider";
import { SidebarConfigProvider } from "@/contexts/sidebar-context";
import { inter } from "@/lib/fonts";
import { AuthProvider } from "@workspace/api";
import { NextIntlClientProvider } from "next-intl";
import { getLocale } from "@/i18n/request";

export const metadata: Metadata = {
  title: "Woder Admin",
  description: "Woder administration dashboard",
};

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const locale = await getLocale()
  const appMessages = (await import(`@/messages/${locale}.json`)).default
  const uiMessages = (await import(`@workspace/ui/messages/${locale}.json`)).default
  const messages = { ...appMessages, common: uiMessages }

  return (
    <html lang={locale} suppressHydrationWarning className={`${inter.variable} antialiased`}>
      <body className={inter.className}>
        <NextIntlClientProvider locale={locale} messages={messages}>
          <AuthProvider>
            <ThemeProvider defaultTheme="system" storageKey="nextjs-ui-theme">
              <SidebarConfigProvider>
                {children}
                <Toaster />
              </SidebarConfigProvider>
            </ThemeProvider>
          </AuthProvider>
        </NextIntlClientProvider>
      </body>
    </html>
  );
}
