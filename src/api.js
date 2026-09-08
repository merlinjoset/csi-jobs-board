// Thin client for the Express + Postgres backend. Vite proxies /api to the
// server during development (see vite.config.js).

async function req(path, { method = "GET", body, userId } = {}) {
  const headers = { "Content-Type": "application/json" }
  if (userId) headers["x-user-id"] = userId
  const res = await fetch(`/api${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  })
  const data = await res.json().catch(() => null)
  if (!res.ok) {
    throw Object.assign(new Error(data?.error || "Request failed"), { status: res.status, data })
  }
  return data
}

export const api = {
  login: (email, password) => req("/login", { method: "POST", body: { email, password } }),
  listJobs: () => req("/jobs"),
  listAllJobs: (userId) => req("/admin/jobs", { userId }),
  createJob: (job, userId) => req("/jobs", { method: "POST", body: job, userId }),
  setStatus: (id, status, userId) =>
    req(`/jobs/${id}/status`, { method: "PATCH", body: { status }, userId }),
  removeJob: (id, userId) => req(`/jobs/${id}`, { method: "DELETE", userId }),
  getShortlist: (userId) => req("/shortlist", { userId }),
  toggleShortlist: (jobId, userId) => req(`/shortlist/${jobId}/toggle`, { method: "POST", userId }),

  listUsers: (userId) => req("/admin/users", { userId }),
  createUser: (data, userId) => req("/admin/users", { method: "POST", body: data, userId }),
  setUserRole: (id, role, userId) =>
    req(`/admin/users/${id}/role`, { method: "PATCH", body: { role }, userId }),
  removeUser: (id, userId) => req(`/admin/users/${id}`, { method: "DELETE", userId }),
}
