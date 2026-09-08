import { useState } from "react"
import { DEMO_USERS } from "../data/users"

export default function Login({ onLogin, onCancel, reason }) {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")

  const [submitting, setSubmitting] = useState(false)

  async function handleSubmit(e) {
    e.preventDefault()
    setSubmitting(true)
    setError("")
    const result = await onLogin(email, password)
    setSubmitting(false)
    if (!result.ok) setError(result.error)
  }

  function fill(user) {
    setEmail(user.email)
    setPassword(user.password)
    setError("")
  }

  return (
    <div className="mx-auto max-w-md">
      <div className="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div className="mb-6 text-center">
          <img src="/csi-logo.png" alt="Church of South India emblem" className="mx-auto h-14 w-14" />
          <h1 className="mt-3 text-2xl font-bold text-stone-900">Sign in</h1>
          <p className="mt-1 text-sm text-stone-600">
            {reason || "Sign in to post opportunities and manage the board."}
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <label className="block">
            <span className="mb-1.5 block text-sm font-medium text-stone-700">Email</span>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="you@example.com"
              className="w-full rounded-lg border border-stone-300 bg-white py-2.5 px-3 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200"
            />
          </label>

          <label className="block">
            <span className="mb-1.5 block text-sm font-medium text-stone-700">Password</span>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              className="w-full rounded-lg border border-stone-300 bg-white py-2.5 px-3 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200"
            />
          </label>

          {error && (
            <p className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>
          )}

          <button
            type="submit"
            disabled={submitting}
            className="w-full rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-900 disabled:opacity-60"
          >
            {submitting ? "Signing in…" : "Sign in"}
          </button>
          {onCancel && (
            <button
              type="button"
              onClick={onCancel}
              className="w-full rounded-lg px-5 py-2 text-sm font-medium text-stone-500 transition hover:text-stone-800"
            >
              Back to board
            </button>
          )}
        </form>

        <div className="mt-6 rounded-xl border border-dashed border-stone-300 bg-stone-50 p-4">
          <p className="text-xs font-semibold uppercase tracking-wide text-stone-500">
            Demo accounts (tap to fill)
          </p>
          <div className="mt-2 space-y-2">
            {DEMO_USERS.map((u) => (
              <button
                key={u.id}
                type="button"
                onClick={() => fill(u)}
                className="flex w-full items-center justify-between rounded-lg bg-white px-3 py-2 text-left text-sm shadow-sm transition hover:ring-2 hover:ring-brand-200"
              >
                <span>
                  <span className="block font-medium text-stone-800">
                    {u.role === "admin" ? "Admin" : "Member"} · {u.name}
                  </span>
                  <span className="block text-xs text-stone-500">
                    {u.email} / {u.password}
                  </span>
                </span>
                <span className="text-xs font-medium text-brand-600">Use</span>
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
