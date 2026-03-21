interface StatsLinkListItem {
  id: string | number
  title: string
}

interface StatsLinkListProps {
  label: string
  items: StatsLinkListItem[]
  hrefPrefix: string
  emptyMessage?: string
}

export function StatsLinkList({ label, items, hrefPrefix, emptyMessage }: StatsLinkListProps) {
  return (
    <div className="space-y-1.5">
      <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
        {label}
      </p>
      {items.length === 0
        ? <p className="text-sm text-muted-foreground">{emptyMessage ?? "—"}</p>
        : (
          <ul className="space-y-1">
            {items.map(item => (
              <li key={item.id} className="flex items-center gap-2 text-sm">
                <span className="size-1.5 rounded-full bg-muted-foreground/40 shrink-0" />
                <a className="text-sm" href={`${hrefPrefix}/${item.id}`} title={item.title}>
                  {item.title}
                </a>
              </li>
            ))}
          </ul>
        )
      }
    </div>
  )
}
