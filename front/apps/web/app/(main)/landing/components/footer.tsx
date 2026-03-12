"use client"

import { env } from "@/lib/env"
import { useTranslations } from "next-intl"

export function LandingFooter() {
  const tc = useTranslations("common")

  return (
    <footer className="border-t bg-background">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="flex flex-col lg:flex-row justify-between items-center gap-2">
          <div className="flex flex-col sm:flex-row items-center gap-2 text-muted-foreground text-sm">
            <div className="flex items-center text-foreground gap-1 font-semibold hover:text-primary">
              <span>© {new Date().getFullYear()}</span>
              <div>
                <a className="font-semibold hover:text-primary" href={env.APP_URL}
                   title={env.APP_NAME}>{env.APP_NAME}</a>.
              </div>
              <span>{tc("all_rights_reserved")}</span>
            </div>
          </div>
          <div className="flex flex-col md:flex-row items-center space-x-4 text-sm text-muted-foreground mt-4 md:mt-0">
            <a href="/privacy" className="text-center hover:text-foreground transition-colors">
              {tc("privacy_policy")}
            </a>
            <span className="hidden md:inline">•</span>
            <a href="/terms" className="text-center hover:text-foreground transition-colors">
              {tc("term_of_service")}
            </a>
            <span className="hidden md:inline">•</span>
            <a href="/cookies" className="text-center hover:text-foreground transition-colors">
              {tc("cookie_policy")}
            </a>
          </div>
        </div>
      </div>
    </footer>
  )
}
