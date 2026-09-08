import { useMemo, useState } from "react"
import Header from "./components/Header"
import Filters from "./components/Filters"
import JobCard from "./components/JobCard"
import JobDetail from "./components/JobDetail"
import PostJobForm from "./components/PostJobForm"
import Login from "./components/Login"
import AdminDashboard from "./components/AdminDashboard"
import AdminUsers from "./components/AdminUsers"
import { useJobs } from "./hooks/useJobs"
import { useAuth } from "./hooks/useAuth"
import { useShortlist } from "./hooks/useShortlist"

export default function App() {
  const { user, login, logout, isAdmin } = useAuth()
  const { jobs, loading, error, addJob, setStatus, removeJob } = useJobs(user)
  const shortlist = useShortlist(user)

  const [view, setView] = useState("list") // list | detail | post | login | admin | shortlist
  const [selected, setSelected] = useState(null)
  const [loginReason, setLoginReason] = useState("")
  const [postedNotice, setPostedNotice] = useState(false)
  const [adminTab, setAdminTab] = useState("postings") // postings | users

  const [query, setQuery] = useState("")
  const [category, setCategory] = useState("All")
  const [type, setType] = useState("All")

  // The public board only shows approved postings.
  const publicJobs = useMemo(() => jobs.filter((j) => j.status === "approved"), [jobs])

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase()
    return publicJobs.filter((job) => {
      const matchesQuery =
        !q ||
        [job.title, job.org, job.description, ...(job.tags ?? [])]
          .join(" ")
          .toLowerCase()
          .includes(q)
      const matchesCategory = category === "All" || job.category === category
      const matchesType = type === "All" || job.type === type
      return matchesQuery && matchesCategory && matchesType
    })
  }, [publicJobs, query, category, type])

  const shortlistedJobs = useMemo(
    () => publicJobs.filter((j) => shortlist.has(j.id)),
    [publicJobs, shortlist]
  )

  function scrollTop() {
    window.scrollTo({ top: 0 })
  }

  function openJob(job) {
    setSelected(job)
    setView("detail")
    scrollTop()
  }

  function goHome() {
    setView("list")
    setSelected(null)
    scrollTop()
  }

  function requirePost() {
    if (!user) {
      setLoginReason("Please sign in to share an opportunity.")
      setView("login")
    } else {
      setPostedNotice(false)
      setView("post")
    }
    scrollTop()
  }

  // Shortlisting is a signed-in feature; prompt sign-in if needed.
  function toggleShortlist(job) {
    if (!user) {
      setLoginReason("Please sign in to shortlist jobs and save them for later.")
      setView("login")
      scrollTop()
      return
    }
    shortlist.toggle(job.id)
  }

  function goLogin() {
    setLoginReason("")
    setView("login")
    scrollTop()
  }

  async function handleLogin(email, password) {
    const result = await login(email, password)
    if (result.ok) {
      setView(result.user.role === "admin" ? "admin" : "list")
      scrollTop()
    }
    return result
  }

  function handleLogout() {
    logout()
    goHome()
  }

  async function handlePost(job) {
    await addJob(job)
    setPostedNotice(true)
    setView("list")
    scrollTop()
  }

  return (
    <div className="min-h-screen">
      <Header
        onHome={goHome}
        onPost={requirePost}
        onLogin={goLogin}
        onLogout={handleLogout}
        onAdmin={() => {
          setView("admin")
          scrollTop()
        }}
        onShortlist={() => {
          setView("shortlist")
          scrollTop()
        }}
        view={view}
        user={user}
        isAdmin={isAdmin}
        shortlistCount={shortlist.count}
      />

      <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {view === "list" && (
          <>
            {postedNotice && (
              <div className="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <svg viewBox="0 0 24 24" className="mt-0.5 h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 9v4M12 17h.01" />
                  <circle cx="12" cy="12" r="9" />
                </svg>
                <p className="text-sm text-amber-800">
                  Thank you for sharing! Your posting is <strong>awaiting review</strong> by a
                  parish administrator before it appears on the board.
                </p>
              </div>
            )}

            <section className="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 to-brand-900 p-8 text-white shadow-sm sm:p-10">
              <div className="flex items-start gap-4">
                <img
                  src="/csi-logo.png"
                  alt=""
                  className="hidden h-16 w-16 rounded-full bg-white/95 p-1.5 shadow-sm sm:block"
                />
                <div>
                  <h1 className="max-w-xl text-2xl font-bold sm:text-3xl">
                    Job openings, shared within our parish family
                  </h1>
                  <p className="mt-2 max-w-2xl text-brand-100">
                    Members of CSI Tamil Parish Dubai share openings they know about, whether at
                    their workplace, their own business, or through their network, to help one
                    another find good work. Know of a role going? Share it.
                  </p>
                  <button
                    onClick={requirePost}
                    className="mt-5 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-brand-800 shadow-sm transition hover:bg-brand-50"
                  >
                    Share an opportunity
                  </button>
                </div>
              </div>
            </section>

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-[300px_1fr]">
              <aside className="lg:sticky lg:top-20 lg:self-start">
                <Filters
                  query={query}
                  setQuery={setQuery}
                  category={category}
                  setCategory={setCategory}
                  type={type}
                  setType={setType}
                  count={filtered.length}
                />
              </aside>

              <div>
                {error ? (
                  <div className="rounded-xl border border-red-200 bg-red-50 p-8 text-center">
                    <p className="font-medium text-red-700">Couldn't load jobs</p>
                    <p className="mt-1 text-sm text-red-600">
                      The API server may be offline. {error}
                    </p>
                  </div>
                ) : loading ? (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {Array.from({ length: 4 }).map((_, i) => (
                      <div key={i} className="h-44 animate-pulse rounded-xl border border-stone-200 bg-stone-100" />
                    ))}
                  </div>
                ) : filtered.length === 0 ? (
                  <div className="rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
                    <p className="font-medium text-stone-700">No openings match your search</p>
                    <p className="mt-1 text-sm text-stone-500">
                      Try adjusting your filters or clearing your search.
                    </p>
                  </div>
                ) : (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {filtered.map((job) => (
                      <JobCard
                        key={job.id}
                        job={job}
                        onOpen={openJob}
                        shortlisted={shortlist.has(job.id)}
                        onToggleShortlist={toggleShortlist}
                      />
                    ))}
                  </div>
                )}
              </div>
            </div>
          </>
        )}

        {view === "shortlist" && (
          <div>
            <h1 className="text-2xl font-bold text-stone-900">My shortlist</h1>
            <p className="mt-1 text-sm text-stone-600">
              Jobs you've saved to revisit. Tap the bookmark on any posting to add or remove it.
            </p>
            {shortlistedJobs.length === 0 ? (
              <div className="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center">
                <p className="font-medium text-stone-700">Your shortlist is empty</p>
                <p className="mt-1 text-sm text-stone-500">
                  Browse the board and bookmark openings you're interested in.
                </p>
                <button
                  onClick={goHome}
                  className="mt-4 rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-900"
                >
                  Browse jobs
                </button>
              </div>
            ) : (
              <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                {shortlistedJobs.map((job) => (
                  <JobCard
                    key={job.id}
                    job={job}
                    onOpen={openJob}
                    shortlisted
                    onToggleShortlist={toggleShortlist}
                  />
                ))}
              </div>
            )}
          </div>
        )}

        {view === "detail" && selected && (
          <JobDetail
            job={selected}
            onBack={goHome}
            shortlisted={shortlist.has(selected.id)}
            onToggleShortlist={toggleShortlist}
          />
        )}

        {view === "post" && <PostJobForm onSubmit={handlePost} onCancel={goHome} />}

        {view === "login" && (
          <Login onLogin={handleLogin} onCancel={goHome} reason={loginReason} />
        )}

        {view === "admin" &&
          (isAdmin ? (
            <div className="mx-auto max-w-5xl">
              <div className="mb-6">
                <h1 className="text-2xl font-bold text-stone-900">Admin dashboard</h1>
                <p className="mt-1 text-sm text-stone-600">
                  Monitor postings and manage parish accounts.
                </p>
              </div>

              <div className="mb-6 inline-flex rounded-lg border border-stone-200 bg-stone-100 p-1">
                {[
                  { key: "postings", label: "Postings" },
                  { key: "users", label: "Users" },
                ].map((t) => (
                  <button
                    key={t.key}
                    onClick={() => setAdminTab(t.key)}
                    className={`rounded-md px-4 py-1.5 text-sm font-medium transition ${
                      adminTab === t.key
                        ? "bg-white text-brand-700 shadow-sm"
                        : "text-stone-600 hover:text-stone-900"
                    }`}
                  >
                    {t.label}
                  </button>
                ))}
              </div>

              {adminTab === "postings" ? (
                <AdminDashboard
                  jobs={jobs}
                  onApprove={(id) => setStatus(id, "approved")}
                  onReject={(id) => setStatus(id, "rejected")}
                  onClose={(id) => setStatus(id, "closed")}
                  onRemove={removeJob}
                  onOpen={openJob}
                />
              ) : (
                <AdminUsers currentUser={user} />
              )}
            </div>
          ) : (
            <div className="mx-auto max-w-md rounded-xl border border-stone-200 bg-white p-8 text-center shadow-sm">
              <p className="font-medium text-stone-800">Admins only</p>
              <p className="mt-1 text-sm text-stone-500">
                Please sign in with an administrator account to view this page.
              </p>
              <button
                onClick={goLogin}
                className="mt-4 rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-900"
              >
                Sign in
              </button>
            </div>
          ))}
      </main>

      <footer className="mt-12 border-t border-stone-200 bg-white">
        <div className="mx-auto max-w-6xl px-4 py-6 text-center text-sm text-stone-500 sm:px-6">
          <p>CSI Tamil Parish Dubai · Parish Jobs Board</p>
          <p className="mt-1 text-xs text-stone-400">
            A member referral board where openings are shared by members to help one another.
            Prototype: accounts and postings are stored in the parish database.
          </p>
        </div>
      </footer>
    </div>
  )
}
