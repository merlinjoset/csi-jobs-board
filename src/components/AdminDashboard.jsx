import { useMemo } from "react"

const STATUS_STYLES = {
  approved: "bg-emerald-100 text-emerald-700",
  pending: "bg-amber-100 text-amber-700",
  rejected: "bg-stone-200 text-stone-600",
  closed: "bg-stone-200 text-stone-600",
}

export default function AdminDashboard({ jobs, onApprove, onReject, onClose, onRemove, onOpen }) {
  const stats = useMemo(() => {
    const weekAgo = Date.now() - 7 * 86400000
    return {
      total: jobs.length,
      approved: jobs.filter((j) => j.status === "approved").length,
      pending: jobs.filter((j) => j.status === "pending").length,
      closed: jobs.filter((j) => j.status === "closed").length,
      thisWeek: jobs.filter((j) => new Date(j.postedAt).getTime() >= weekAgo).length,
    }
  }, [jobs])

  const pending = jobs.filter((j) => j.status === "pending")

  return (
    <div>
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-5">
        <StatCard label="Total postings" value={stats.total} />
        <StatCard label="Live on board" value={stats.approved} tone="emerald" />
        <StatCard label="Awaiting review" value={stats.pending} tone="amber" />
        <StatCard label="Closed" value={stats.closed} />
        <StatCard label="Posted this week" value={stats.thisWeek} tone="brand" />
      </div>

      {pending.length > 0 && (
        <section className="mt-8">
          <h2 className="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-stone-500">
            Review queue
            <span className="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700">
              {pending.length}
            </span>
          </h2>
          <div className="mt-3 space-y-3">
            {pending.map((job) => (
              <div
                key={job.id}
                className="flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50/50 p-4 sm:flex-row sm:items-center sm:justify-between"
              >
                <div className="min-w-0">
                  <button onClick={() => onOpen(job)} className="text-left">
                    <p className="truncate font-semibold text-stone-900 hover:text-brand-700">{job.title}</p>
                  </button>
                  <p className="text-sm text-stone-500">
                    {job.org} · posted by {job.postedBy} · {job.category}
                  </p>
                </div>
                <div className="flex shrink-0 gap-2">
                  <button
                    onClick={() => onApprove(job.id)}
                    className="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                  >
                    Approve
                  </button>
                  <button
                    onClick={() => onReject(job.id)}
                    className="rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                  >
                    Reject
                  </button>
                </div>
              </div>
            ))}
          </div>
        </section>
      )}

      <section className="mt-8">
        <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">All postings</h2>
        <div className="mt-3 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                <tr>
                  <th className="px-4 py-3 font-medium">Posting</th>
                  <th className="px-4 py-3 font-medium">Category</th>
                  <th className="px-4 py-3 font-medium">Posted</th>
                  <th className="px-4 py-3 font-medium">Status</th>
                  <th className="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-100">
                {jobs.map((job) => (
                  <tr key={job.id} className="hover:bg-stone-50/60">
                    <td className="px-4 py-3">
                      <button onClick={() => onOpen(job)} className="text-left">
                        <span className="block font-medium text-stone-900 hover:text-brand-700">{job.title}</span>
                        <span className="block text-xs text-stone-500">{job.org}</span>
                      </button>
                    </td>
                    <td className="px-4 py-3 text-stone-600">{job.category}</td>
                    <td className="px-4 py-3 text-stone-500">{job.postedAt}</td>
                    <td className="px-4 py-3">
                      <span
                        className={`rounded-full px-2 py-0.5 text-xs font-medium capitalize ${
                          STATUS_STYLES[job.status] ?? "bg-stone-100 text-stone-600"
                        }`}
                      >
                        {job.status ?? "approved"}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex justify-end gap-1">
                        {job.status === "pending" && (
                          <button
                            onClick={() => onApprove(job.id)}
                            className="rounded-md px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50"
                          >
                            Approve
                          </button>
                        )}
                        {job.status === "approved" && (
                          <button
                            onClick={() => onClose(job.id)}
                            className="rounded-md px-2 py-1 text-xs font-medium text-stone-600 hover:bg-stone-100"
                          >
                            Close
                          </button>
                        )}
                        {(job.status === "closed" || job.status === "rejected") && (
                          <button
                            onClick={() => onApprove(job.id)}
                            className="rounded-md px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-50"
                          >
                            Reopen
                          </button>
                        )}
                        <button
                          onClick={() => onRemove(job.id)}
                          className="rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                        >
                          Remove
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  )
}

const TONE = {
  default: "text-stone-900",
  emerald: "text-emerald-600",
  amber: "text-amber-600",
  brand: "text-brand-700",
}

function StatCard({ label, value, tone = "default" }) {
  return (
    <div className="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
      <p className="text-xs font-medium uppercase tracking-wide text-stone-500">{label}</p>
      <p className={`mt-1 text-3xl font-bold ${TONE[tone]}`}>{value}</p>
    </div>
  )
}
