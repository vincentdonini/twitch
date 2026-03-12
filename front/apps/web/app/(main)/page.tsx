"use client"

import React from "react"
import { PageContainer } from "@/ui/components/page-container"
import { HeroSection } from "@/app/(main)/landing/components/hero-section"
import { StatsSection } from "@/app/(main)/landing/components/stats-section"
import { BlogComingSoonSection } from "@/app/(main)/landing/components/blog-coming-soon-section"

export default function Page() {
  return (
    <PageContainer size="full" withPadding={false}>

      <HeroSection />
      <StatsSection />
      <BlogComingSoonSection />

    </PageContainer>
  )
}
