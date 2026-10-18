import { SessionsKpi } from "./_components/sessions-kpi"
import { SessionsFrequencyChart } from "./_components/sessions-frequency-chart"
import { UpcomingSessions } from "./_components/upcoming-sessions"
import { SessionsHistory } from "./_components/sessions-history"

export default function Page() {
  return (
    <div className="flex flex-col gap-6 px-4 lg:px-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold tracking-tight">Sessions</h1>
        <p className="text-muted-foreground mt-1">Track your training history and upcoming sessions.</p>
      </div>

      {/* KPI */}
      <SessionsKpi />

      {/* Chart */}
      <div className="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <div className="col-span-12">
          <SessionsFrequencyChart />
        </div>
      </div>

      {/* History */}
      <SessionsHistory />
    </div>
  )
}
