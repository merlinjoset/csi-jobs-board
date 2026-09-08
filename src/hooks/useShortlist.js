import { useCallback, useEffect, useState } from "react"
import { api } from "../api"

// Per-user shortlist backed by the database. Empty when signed out.
export function useShortlist(user) {
  const [ids, setIds] = useState([])

  const refresh = useCallback(async () => {
    if (!user) {
      setIds([])
      return
    }
    try {
      setIds(await api.getShortlist(user.id))
    } catch {
      setIds([])
    }
  }, [user?.id])

  useEffect(() => {
    refresh()
  }, [refresh])

  async function toggle(jobId) {
    if (!user) return
    try {
      const next = await api.toggleShortlist(jobId, user.id)
      setIds(next)
    } catch {
      // ignore transient errors in the prototype
    }
  }

  return { ids, count: ids.length, has: (id) => ids.includes(id), toggle, refresh }
}
