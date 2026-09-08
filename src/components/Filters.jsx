import { CATEGORIES, JOB_TYPES } from "../data/jobs"

export default function Filters({ query, setQuery, category, setCategory, type, setType, count }) {
  const hasFilters = query || category !== "All" || type !== "All"

  return (
    <div className="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
      <div className="relative">
        <svg
          viewBox="0 0 24 24"
          className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
        >
          <circle cx="11" cy="11" r="7" />
          <path d="M21 21l-4-4" />
        </svg>
        <input
          type="text"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search by title, organization, or keyword…"
          className="w-full rounded-lg border border-stone-300 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200"
        />
      </div>

      <div className="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <label className="block">
          <span className="mb-1 block text-xs font-medium text-stone-500">Category</span>
          <select
            value={category}
            onChange={(e) => setCategory(e.target.value)}
            className="w-full rounded-lg border border-stone-300 bg-white py-2 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200"
          >
            <option>All</option>
            {CATEGORIES.map((c) => (
              <option key={c}>{c}</option>
            ))}
          </select>
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-stone-500">Job type</span>
          <select
            value={type}
            onChange={(e) => setType(e.target.value)}
            className="w-full rounded-lg border border-stone-300 bg-white py-2 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200"
          >
            <option>All</option>
            {JOB_TYPES.map((t) => (
              <option key={t}>{t}</option>
            ))}
          </select>
        </label>
      </div>

      <div className="mt-3 flex items-center justify-between">
        <span className="text-xs text-stone-500">
          {count} {count === 1 ? "opening" : "openings"} found
        </span>
        {hasFilters && (
          <button
            onClick={() => {
              setQuery("")
              setCategory("All")
              setType("All")
            }}
            className="text-xs font-medium text-brand-600 hover:text-brand-700"
          >
            Clear filters
          </button>
        )}
      </div>
    </div>
  )
}
