import { useState } from "react"

export default function JobDetail({ job, onBack, shortlisted, onToggleShortlist }) {
  const [applied, setApplied] = useState(false)

  return (
    <div className="mx-auto max-w-3xl">
      <button
        onClick={onBack}
        className="mb-4 inline-flex items-center gap-1 text-sm font-medium text-stone-500 hover:text-brand-700"
      >
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        Back to all jobs
      </button>

      <article className="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div className="border-b border-stone-100 bg-gradient-to-br from-brand-50 to-warm-50 p-6 sm:p-8">
          <div className="flex items-start justify-between gap-3">
            <div className="flex flex-wrap items-center gap-2">
              <span className="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-brand-700 shadow-sm">
                {job.category}
              </span>
              <span className="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-stone-600 shadow-sm">
                {job.type}
              </span>
            </div>
            <button
              onClick={() => onToggleShortlist(job)}
              className={`inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium shadow-sm transition ${
                shortlisted
                  ? "bg-warm-500 text-white hover:bg-warm-600"
                  : "bg-white text-stone-700 hover:bg-stone-50"
              }`}
            >
              <svg viewBox="0 0 24 24" className="h-4 w-4" fill={shortlisted ? "currentColor" : "none"} stroke="currentColor" strokeWidth="2">
                <path d="M6 4h12a1 1 0 011 1v15l-7-4-7 4V5a1 1 0 011-1z" />
              </svg>
              {shortlisted ? "Shortlisted" : "Shortlist"}
            </button>
          </div>
          <h1 className="mt-3 text-2xl font-bold text-stone-900 sm:text-3xl">{job.title}</h1>
          <p className="mt-1 text-stone-600">{job.org}</p>

          <div className="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <Detail label="Location" value={job.location} />
            <Detail label="Compensation" value={job.pay} />
            <Detail label="Shared by" value={job.postedBy} />
          </div>
        </div>

        <div className="p-6 sm:p-8">
          {job.sharedNote && (
            <div className="mb-6 flex items-start gap-3 rounded-xl border border-brand-100 bg-brand-50/60 p-4">
              <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                {job.postedBy.charAt(0)}
              </span>
              <p className="text-sm text-stone-700">
                <span className="font-semibold text-stone-900">{job.postedBy}</span> shared this
                opening: <span className="italic">"{job.sharedNote}"</span>
              </p>
            </div>
          )}

          <section>
            <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">About this role</h2>
            <p className="mt-2 whitespace-pre-line leading-relaxed text-stone-700">{job.description}</p>
          </section>

          {job.requirements?.length > 0 && (
            <section className="mt-6">
              <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">What we're looking for</h2>
              <ul className="mt-2 space-y-2">
                {job.requirements.map((r, i) => (
                  <li key={i} className="flex items-start gap-2 text-stone-700">
                    <svg viewBox="0 0 24 24" className="mt-0.5 h-4 w-4 shrink-0 text-brand-500" fill="none" stroke="currentColor" strokeWidth="2.5">
                      <path d="M20 6L9 17l-5-5" />
                    </svg>
                    {r}
                  </li>
                ))}
              </ul>
            </section>
          )}

          <section className="mt-8 rounded-xl bg-stone-50 p-5">
            {applied ? (
              <div className="text-center">
                <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                  <svg viewBox="0 0 24 24" className="h-6 w-6" fill="none" stroke="currentColor" strokeWidth="2.5">
                    <path d="M20 6L9 17l-5-5" />
                  </svg>
                </div>
                <p className="mt-3 font-semibold text-stone-900">Your interest has been noted!</p>
                <p className="mt-1 text-sm text-stone-600">
                  Reach out directly at{" "}
                  <a href={`mailto:${job.contactEmail}`} className="font-medium text-brand-700 underline">
                    {job.contactEmail}
                  </a>{" "}
                  to continue the conversation.
                </p>
              </div>
            ) : (
              <div className="flex flex-col items-center justify-between gap-3 sm:flex-row sm:text-left">
                <div>
                  <p className="font-semibold text-stone-900">Interested in this opportunity?</p>
                  <p className="text-sm text-stone-600">Let the poster know you'd like to connect.</p>
                </div>
                <button
                  onClick={() => setApplied(true)}
                  className="w-full shrink-0 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 sm:w-auto"
                >
                  Express interest
                </button>
              </div>
            )}
          </section>
        </div>
      </article>
    </div>
  )
}

function Detail({ label, value }) {
  return (
    <div className="rounded-lg bg-white/70 p-3">
      <p className="text-xs font-medium uppercase tracking-wide text-stone-400">{label}</p>
      <p className="mt-0.5 text-sm font-medium text-stone-800">{value}</p>
    </div>
  )
}
