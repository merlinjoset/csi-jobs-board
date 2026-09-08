import { useEffect, useState } from "react"
import { api } from "../api"

const SESSION_KEY = "gcjobs.session.v1"

function loadSession() {
  try {
    const raw = localStorage.getItem(SESSION_KEY)
    if (raw) return JSON.parse(raw)
  } catch {
    // ignore
  }
  return null
}

// Auth against the backend. The signed-in user (no password) is cached in
// localStorage so the session survives a refresh; the user's id is sent as a
// header on protected API calls.
export function useAuth() {
  const [user, setUser] = useState(loadSession)

  useEffect(() => {
    try {
      if (user) localStorage.setItem(SESSION_KEY, JSON.stringify(user))
      else localStorage.removeItem(SESSION_KEY)
    } catch {
      // storage unavailable, non-fatal
    }
  }, [user])

  async function login(email, password) {
    try {
      const res = await api.login(email, password)
      if (!res.ok) return { ok: false, error: res.error || "Incorrect email or password." }
      setUser(res.user)
      return { ok: true, user: res.user }
    } catch {
      return { ok: false, error: "Could not reach the server. Is the API running?" }
    }
  }

  function logout() {
    setUser(null)
  }

  return { user, login, logout, isAdmin: user?.role === "admin" }
}
