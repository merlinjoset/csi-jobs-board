import { useEffect, useRef, useState } from "react"

export default function Header({
  onHome,
  onPost,
  onLogin,
  onLogout,
  onAdmin,
  onShortlist,
  view,
  user,
  isAdmin,
  shortlistCount = 0,
}) {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [userMenuOpen, setUserMenuOpen] = useState(false)
  const userMenuRef = useRef(null)

  // Close the user dropdown when clicking outside it.
  useEffect(() => {
    function onClick(e) {
      if (userMenuRef.current && !userMenuRef.current.contains(e.target)) {
        setUserMenuOpen(false)
      }
    }
    document.addEventListener("mousedown", onClick)
    return () => document.removeEventListener("mousedown", onClick)
  }, [])

  const navItems = [
    { key: "list", label: "Browse jobs", onClick: onHome, show: true },
    {
      key: "shortlist",
      label: "Shortlist",
      onClick: onShortlist,
      show: !!user,
      badge: shortlistCount,
    },
    { key: "admin", label: "Admin", onClick: onAdmin, show: isAdmin },
  ].filter((i) => i.show)

  function handleNav(fn) {
    setMobileOpen(false)
    fn()
  }

  return (
    <header className="sticky top-0 z-30 border-b border-stone-200 bg-white/90 shadow-sm backdrop-blur">
      {/* Brand accent bar */}
      <div className="h-1 w-full bg-gradient-to-r from-brand-800 via-warm-500 to-brand-800" />

      <div className="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
        {/* Logo */}
        <button
          onClick={() => handleNav(onHome)}
          className="flex items-center gap-2.5 text-left"
          aria-label="Go to job listings"
        >
          <img src="/csi-logo.png" alt="Church of South India emblem" className="h-10 w-10 shrink-0" />
          <span className="leading-tight">
            <span className="block text-base font-bold text-stone-900">CSI Tamil Parish Dubai</span>
            <span className="block text-xs text-stone-500">Parish Jobs Board</span>
          </span>
        </button>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-1 md:flex">
          {navItems.map((item) => (
            <NavLink key={item.key} active={view === item.key} onClick={() => item.onClick()} badge={item.badge}>
              {item.label}
            </NavLink>
          ))}
        </nav>

        {/* Desktop actions */}
        <div className="hidden items-center gap-2 md:flex">
          <button
            onClick={onPost}
            className="inline-flex items-center gap-1.5 rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-900"
          >
            <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round">
              <path d="M12 5v14M5 12h14" />
            </svg>
            Share an opportunity
          </button>

          {user ? (
            <div className="relative" ref={userMenuRef}>
              <button
                onClick={() => setUserMenuOpen((o) => !o)}
                className="flex items-center gap-2 rounded-lg py-1.5 pl-1.5 pr-2 transition hover:bg-stone-100"
              >
                <span className="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                  {user.name.charAt(0)}
                </span>
                <span className="hidden text-left leading-tight lg:block">
                  <span className="block text-sm font-medium text-stone-800">{user.name}</span>
                  <span className="block text-xs capitalize text-stone-400">{user.role}</span>
                </span>
                <svg viewBox="0 0 24 24" className={`h-4 w-4 text-stone-400 transition ${userMenuOpen ? "rotate-180" : ""}`} fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M6 9l6 6 6-6" />
                </svg>
              </button>

              {userMenuOpen && (
                <div className="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-stone-200 bg-white py-1 shadow-lg">
                  <div className="border-b border-stone-100 px-4 py-2.5">
                    <p className="text-sm font-medium text-stone-800">{user.name}</p>
                    <p className="truncate text-xs text-stone-500">{user.email}</p>
                  </div>
                  <MenuItem onClick={() => { setUserMenuOpen(false); onShortlist() }}>
                    My shortlist{shortlistCount > 0 ? ` (${shortlistCount})` : ""}
                  </MenuItem>
                  {isAdmin && (
                    <MenuItem onClick={() => { setUserMenuOpen(false); onAdmin() }}>Admin dashboard</MenuItem>
                  )}
                  <MenuItem onClick={() => { setUserMenuOpen(false); onLogout() }} danger>
                    Sign out
                  </MenuItem>
                </div>
              )}
            </div>
          ) : (
            <button
              onClick={onLogin}
              className="rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
            >
              Sign in
            </button>
          )}
        </div>

        {/* Mobile toggle */}
        <button
          onClick={() => setMobileOpen((o) => !o)}
          className="rounded-lg p-2 text-stone-600 transition hover:bg-stone-100 md:hidden"
          aria-label="Toggle menu"
          aria-expanded={mobileOpen}
        >
          <svg viewBox="0 0 24 24" className="h-6 w-6" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
            {mobileOpen ? <path d="M6 6l12 12M18 6L6 18" /> : <path d="M4 7h16M4 12h16M4 17h16" />}
          </svg>
        </button>
      </div>

      {/* Mobile panel */}
      {mobileOpen && (
        <div className="border-t border-stone-200 bg-white px-4 py-3 md:hidden">
          {user && (
            <div className="mb-2 flex items-center gap-2.5 rounded-lg bg-stone-50 px-3 py-2.5">
              <span className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                {user.name.charAt(0)}
              </span>
              <span className="leading-tight">
                <span className="block text-sm font-medium text-stone-800">{user.name}</span>
                <span className="block text-xs capitalize text-stone-400">{user.role}</span>
              </span>
            </div>
          )}

          <nav className="flex flex-col">
            {navItems.map((item) => (
              <button
                key={item.key}
                onClick={() => handleNav(item.onClick)}
                className={`flex items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium transition ${
                  view === item.key ? "bg-brand-50 text-brand-700" : "text-stone-700 hover:bg-stone-50"
                }`}
              >
                {item.label}
                {item.badge > 0 && (
                  <span className="rounded-full bg-warm-500 px-1.5 py-0.5 text-xs font-bold text-white">
                    {item.badge}
                  </span>
                )}
              </button>
            ))}
          </nav>

          <div className="mt-3 flex flex-col gap-2 border-t border-stone-100 pt-3">
            <button
              onClick={() => handleNav(onPost)}
              className="rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-900"
            >
              + Share an opportunity
            </button>
            {user ? (
              <button
                onClick={() => handleNav(onLogout)}
                className="rounded-lg border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
              >
                Sign out
              </button>
            ) : (
              <button
                onClick={() => handleNav(onLogin)}
                className="rounded-lg border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
              >
                Sign in
              </button>
            )}
          </div>
        </div>
      )}
    </header>
  )
}

function NavLink({ active, onClick, badge, children }) {
  return (
    <button
      onClick={onClick}
      className={`relative inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition ${
        active ? "bg-brand-50 text-brand-700" : "text-stone-600 hover:bg-stone-100 hover:text-stone-900"
      }`}
    >
      {children}
      {badge > 0 && (
        <span className="rounded-full bg-warm-500 px-1.5 py-0.5 text-xs font-bold text-white">{badge}</span>
      )}
    </button>
  )
}

function MenuItem({ onClick, danger, children }) {
  return (
    <button
      onClick={onClick}
      className={`block w-full px-4 py-2 text-left text-sm transition hover:bg-stone-50 ${
        danger ? "text-red-600" : "text-stone-700"
      }`}
    >
      {children}
    </button>
  )
}
