import { useCallback, useEffect, useState } from "react"
import { api } from "../api"

// Loads postings from the backend. Admins get every posting (any status);
// everyone else gets the approved public board.
export function useJobs(user) {
  const [jobs, setJobs] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  const refresh = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const data =
        user?.role === "admin" ? await api.listAllJobs(user.id) : await api.listJobs()
      setJobs(data)
    } catch (e) {
      setError(e.message || "Failed to load jobs")
    } finally {
      setLoading(false)
    }
  }, [user?.id, user?.role])

  useEffect(() => {
    refresh()
  }, [refresh])

  async function addJob(job) {
    const created = await api.createJob(job, user?.id)
    await refresh()
    return created
  }

  async function setStatus(id, status) {
    await api.setStatus(id, status, user?.id)
    await refresh()
  }

  async function removeJob(id) {
    await api.removeJob(id, user?.id)
    await refresh()
  }

  return { jobs, loading, error, addJob, setStatus, removeJob, refresh }
}
