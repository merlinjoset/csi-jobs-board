import { useEffect, useMemo, useState } from "react"
import { api } from "../api"

export default function AdminUsers({ currentUser }) {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [showAdd, setShowAdd] = useState(false)

  async function refresh() {
    setLoading(true)
    setError("")
    try {
      setUsers(await api.listUsers(currentUser.id))
    } catch (e) {
      setError(e.message || "Failed to load users")
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    refresh()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const stats = useMemo(
    () => ({
      total: users.length,
      admins: users.filter((u) => u.role === "admin").length,
      members: users.filter((u) => u.role === "member").length,
    }),
    [users]
  )

  async function changeRole(u, role) {
    setError("")
    try {
      await api.setUserRole(u.id, role, currentUser.id)
      await refresh()
    } catch (e) {
      setError(e.message)
    }
  }

  async function removeUser(u) {
    setError("")
    try {
      await api.removeUser(u.id, currentUser.id)
      await refresh()
    } catch (e) {
      setError(e.message)
    }
  }

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-bold text-stone-900">Users</h2>
          <p className="mt-1 text-sm text-stone-600">
            Manage member and administrator accounts.
          </p>
        </div>
        <button
          onClick={() => setShowAdd((s) => !s)}
          className="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-900"
        >
          {showAdd ? "Close" : "+ Add user"}
        </button>
      </div>

      <div className="grid grid-cols-3 gap-4">
        <StatCard label="Total users" value={stats.total} />
        <StatCard label="Administrators" value={stats.admins} tone="brand" />
        <StatCard label="Members" value={stats.members} tone="emerald" />
      </div>

      {showAdd && (
        <AddUserForm
          currentUser={currentUser}
          onDone={() => {
            setShowAdd(false)
            refresh()
          }}
        />
      )}

      {error && (
        <p className="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>
      )}

      <div className="mt-6 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
              <tr>
                <th className="px-4 py-3 font-medium">User</th>
                <th className="px-4 py-3 font-medium">Role</th>
                <th className="px-4 py-3 font-medium text-center">Postings</th>
                <th className="px-4 py-3 font-medium text-center">Shortlisted</th>
                <th className="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100">
              {loading ? (
                <tr>
                  <td colSpan={5} className="px-4 py-8 text-center text-stone-400">
                    Loading users…
                  </td>
                </tr>
              ) : (
                users.map((u) => {
                  const isSelf = u.id === currentUser.id
                  return (
                    <tr key={u.id} className="hover:bg-stone-50/60">
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-2.5">
                          <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                            {u.name.charAt(0)}
                          </span>
                          <div>
                            <span className="block font-medium text-stone-900">
                              {u.name}
                              {isSelf && (
                                <span className="ml-1.5 text-xs font-normal text-stone-400">(you)</span>
                              )}
                            </span>
                            <span className="block text-xs text-stone-500">{u.email}</span>
                          </div>
                        </div>
                      </td>
                      <td className="px-4 py-3">
                        <span
                          className={`rounded-full px-2 py-0.5 text-xs font-medium capitalize ${
                            u.role === "admin"
                              ? "bg-brand-100 text-brand-700"
                              : "bg-stone-100 text-stone-600"
                          }`}
                        >
                          {u.role}
                        </span>
                      </td>
                      <td className="px-4 py-3 text-center text-stone-600">{u.jobsPosted}</td>
                      <td className="px-4 py-3 text-center text-stone-600">{u.shortlistCount}</td>
                      <td className="px-4 py-3">
                        <div className="flex justify-end gap-1">
                          {u.role === "member" ? (
                            <button
                              onClick={() => changeRole(u, "admin")}
                              className="rounded-md px-2 py-1 text-xs font-medium text-brand-700 hover:bg-brand-50"
                            >
                              Make admin
                            </button>
                          ) : (
                            <button
                              onClick={() => changeRole(u, "member")}
                              disabled={isSelf}
                              className="rounded-md px-2 py-1 text-xs font-medium text-stone-600 hover:bg-stone-100 disabled:opacity-40"
                              title={isSelf ? "You can't change your own role" : ""}
                            >
                              Make member
                            </button>
                          )}
                          <button
                            onClick={() => removeUser(u)}
                            disabled={isSelf}
                            className="rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 disabled:opacity-40"
                            title={isSelf ? "You can't delete your own account" : ""}
                          >
                            Remove
                          </button>
                        </div>
                      </td>
                    </tr>
                  )
                })
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}

function AddUserForm({ currentUser, onDone }) {
  const [form, setForm] = useState({ name: "", email: "", password: "", role: "member" })
  const [error, setError] = useState("")
  const [saving, setSaving] = useState(false)

  function set(k, v) {
    setForm((f) => ({ ...f, [k]: v }))
  }

  async function submit(e) {
    e.preventDefault()
    setError("")
    setSaving(true)
    try {
      await api.createUser(form, currentUser.id)
      onDone()
    } catch (err) {
      setError(err.message)
    } finally {
      setSaving(false)
    }
  }

  const input =
    "w-full rounded-lg border border-stone-300 bg-white py-2 px-3 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200"

  return (
    <form onSubmit={submit} className="mt-4 rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
      <h3 className="text-sm font-semibold text-stone-800">Add a new user</h3>
      <div className="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <input className={input} placeholder="Full name" value={form.name} onChange={(e) => set("name", e.target.value)} />
        <input className={input} placeholder="Email" value={form.email} onChange={(e) => set("email", e.target.value)} />
        <input className={input} type="password" placeholder="Temporary password" value={form.password} onChange={(e) => set("password", e.target.value)} />
        <select className={input} value={form.role} onChange={(e) => set("role", e.target.value)}>
          <option value="member">Member</option>
          <option value="admin">Administrator</option>
        </select>
      </div>
      {error && <p className="mt-3 text-sm text-red-600">{error}</p>}
      <div className="mt-4 flex justify-end gap-2">
        <button
          type="submit"
          disabled={saving}
          className="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-900 disabled:opacity-60"
        >
          {saving ? "Adding…" : "Add user"}
        </button>
      </div>
    </form>
  )
}

const TONE = { default: "text-stone-900", brand: "text-brand-700", emerald: "text-emerald-600" }

function StatCard({ label, value, tone = "default" }) {
  return (
    <div className="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
      <p className="text-xs font-medium uppercase tracking-wide text-stone-500">{label}</p>
      <p className={`mt-1 text-3xl font-bold ${TONE[tone]}`}>{value}</p>
    </div>
  )
}
