import { LandingNavbar } from "@/app/landing/components/navbar"
import React from "react"
import { LandingFooter } from "@/app/landing/components/footer"

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-background">
      <LandingNavbar />
      {children}
      <LandingFooter />
    </div>
  )
}