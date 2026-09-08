const TYPE_STYLES = {
  "Full-time": "bg-emerald-100 text-emerald-700",
  "Part-time": "bg-sky-100 text-sky-700",
  Contract: "bg-amber-100 text-amber-700",
  Temporary: "bg-orange-100 text-orange-700",
  Volunteer: "bg-brand-100 text-brand-700",
}

function timeAgo(dateStr) {
  const then = new Date(dateStr)
  const days = Math.round((Date.now() - then.getTime()) / 86400000)
  if (days <= 0) return "Today"
  if (days === 1) return "Yesterday"
  if (days < 7) return `${days} days ago`
  const weeks = Math.round(days / 7)
  return weeks === 1 ? "1 week ago" : `${weeks} weeks ago`
}

export default function JobCard({ job, onOpen, shortlisted, onToggleShortlist }) {
  return (
    <div
      onClick={() => onOpen(job)}
      role="button"
      tabIndex={0}
      onKeyDown={(e) => (e.key === "Enter" || e.key === " ") && onOpen(job)}
      className="group flex w-full cursor-pointer flex-col rounded-xl border border-stone-200 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"
    >
      <div className="flex items-start justify-between gap-3">
        <div className="min-w-0">
          <h3 className="font-semibold text-stone-900 group-hover:text-brand-700">{job.title}</h3>
          <p className="text-sm text-stone-500">{job.org}</p>
        </div>
        <div className="flex shrink-0 items-center gap-1.5">
          <span
            className={`rounded-full px-2.5 py-1 text-xs font-medium ${
              TYPE_STYLES[job.type] ?? "bg-stone-100 text-stone-600"
            }`}
          >
            {job.type}
          </span>
          <button
            onClick={(e) => {
              e.stopPropagation()
              onToggleShortlist(job)
            }}
            aria-label={shortlisted ? "Remove from shortlist" : "Add to shortlist"}
            title={shortlisted ? "Shortlisted" : "Add to shortlist"}
            className={`rounded-lg p-1.5 transition ${
              shortlisted
                ? "text-warm-500 hover:bg-warm-50"
                : "text-stone-300 hover:bg-stone-100 hover:text-stone-500"
            }`}
          >
            <svg viewBox="0 0 24 24" className="h-4.5 w-4.5" fill={shortlisted ? "currentColor" : "none"} stroke="currentColor" strokeWidth="2">
              <path d="M6 4h12a1 1 0 011 1v15l-7-4-7 4V5a1 1 0 011-1z" />
            </svg>
          </button>
        </div>
      </div>

      <p className="mt-3 line-clamp-2 text-sm text-stone-600">{job.description}</p>

      <div className="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-stone-500">
        <span className="inline-flex items-center gap-1">
          <svg viewBox="0 0 24 24" className="h-3.5 w-3.5" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M12 21s-7-5.3-7-11a7 7 0 1114 0c0 5.7-7 11-7 11z" />
            <circle cx="12" cy="10" r="2.5" />
          </svg>
          {job.location}
        </span>
        <span className="inline-flex items-center gap-1">
          <svg viewBox="0 0 24 24" className="h-3.5 w-3.5" fill="none" stroke="currentColor" strokeWidth="2">
            <rect x="2" y="6" width="20" height="12" rx="2" />
            <circle cx="12" cy="12" r="2.5" />
            <path d="M6 10v4M18 10v4" />
          </svg>
          {job.pay}
        </span>
      </div>

      <div className="mt-4 flex items-center justify-between border-t border-stone-100 pt-3">
        <span className="text-xs font-medium text-brand-600">{job.category}</span>
        <span className="text-xs text-stone-400">{timeAgo(job.postedAt)}</span>
      </div>
    </div>
  )
}
