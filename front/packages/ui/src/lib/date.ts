export function formatDate(
  iso: string,
  locale?: string,
  opts: Intl.DateTimeFormatOptions = { month: "short", day: "numeric" },
): string {
  return new Date(iso).toLocaleDateString(locale, opts)
}
