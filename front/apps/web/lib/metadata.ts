import type { Metadata } from "next"
import { env } from "@/lib/env"

export const APP_NAME = env.APP_NAME
const APP_URL = env.APP_URL

const defaults: Metadata = {
  applicationName: APP_NAME,
  metadataBase: new URL(APP_URL),
  openGraph: {
    siteName: APP_NAME,
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
  },
}

export function createMetadata(overrides: Metadata): Metadata {
  return {
    ...defaults,
    ...overrides,
    openGraph: {
      ...defaults.openGraph,
      ...overrides.openGraph,
      title: overrides.openGraph?.title ?? overrides.title ?? defaults.openGraph?.title,
    },
    twitter: {
      ...defaults.twitter,
      ...overrides.twitter,
      title: overrides.twitter?.title ?? overrides.title ?? defaults.twitter?.title,
    },
  }
}
