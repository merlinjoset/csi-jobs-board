import { useState } from "react"
import { CATEGORIES, JOB_TYPES } from "../data/jobs"

const EMPTY = {
  title: "",
  org: "",
  postedBy: "",
  category: CATEGORIES[0],
  type: JOB_TYPES[0],
  location: "",
  pay: "",
  payPeriod: "per month",
  description: "",
  requirements: "",
  sharedNote: "",
  contactEmail: "",
}

export default function PostJobForm({ onSubmit, onCancel }) {
  const [form, setForm] = useState(EMPTY)
  const [errors, setErrors] = useState({})

  function update(field, value) {
    setForm((f) => ({ ...f, [field]: value }))
  }

  function validate() {
    const e = {}
    if (!form.title.trim()) e.title = "Please add a job title"
    if (!form.org.trim()) e.org = "Please add the employer or company name"
    if (!form.postedBy.trim()) e.postedBy = "Please add your name"
    if (!form.location.trim()) e.location = "Please add a location"
    if (!form.contactEmail.trim()) e.contactEmail = "Please add a contact email"
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.contactEmail))
      e.contactEmail = "That doesn't look like a valid email"
    if (!form.description.trim()) e.description = "Please describe the role"
    setErrors(e)
    return Object.keys(e).length === 0
  }

  function handleSubmit(ev) {
    ev.preventDefault()
    if (!validate()) return
    const requirements = form.requirements
      .split("\n")
      .map((r) => r.trim())
      .filter(Boolean)
    const amount = form.pay.trim()
    const pay = amount ? `${amount} / ${form.payPeriod.replace(/^per /, "")}` : "Not specified"
    onSubmit({
      ...form,
      pay,
      requirements,
      tags: [],
    })
  }

  return (
    <div className="mx-auto max-w-2xl">
      <button
        onClick={onCancel}
        className="mb-4 inline-flex items-center gap-1 text-sm font-medium text-stone-500 hover:text-brand-700"
      >
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        Cancel
      </button>

      <div className="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 className="text-2xl font-bold text-stone-900">Share an opportunity</h1>
        <p className="mt-1 text-sm text-stone-600">
          Know of a job going at your workplace, your business, or through someone you know?
          Share it here to help a fellow member find work. A parish admin reviews each
          posting before it goes live.
        </p>

        <form onSubmit={handleSubmit} className="mt-6 space-y-5">
          <Field label="Job title" error={errors.title}>
            <input
              value={form.title}
              onChange={(e) => update("title", e.target.value)}
              placeholder="e.g. Office Administrator"
              className={inputCls(errors.title)}
            />
          </Field>

          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <Field label="Employer / company with the opening" error={errors.org}>
              <input
                value={form.org}
                onChange={(e) => update("org", e.target.value)}
                placeholder="e.g. Gulf Star Trading LLC"
                className={inputCls(errors.org)}
              />
            </Field>
            <Field label="Your name" error={errors.postedBy}>
              <input
                value={form.postedBy}
                onChange={(e) => update("postedBy", e.target.value)}
                placeholder="e.g. Sarah Mitchell"
                className={inputCls(errors.postedBy)}
              />
            </Field>
          </div>

          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <Field label="Category">
              <select
                value={form.category}
                onChange={(e) => update("category", e.target.value)}
                className={inputCls()}
              >
                {CATEGORIES.map((c) => (
                  <option key={c}>{c}</option>
                ))}
              </select>
            </Field>
            <Field label="Job type">
              <select value={form.type} onChange={(e) => update("type", e.target.value)} className={inputCls()}>
                {JOB_TYPES.map((t) => (
                  <option key={t}>{t}</option>
                ))}
              </select>
            </Field>
          </div>

          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <Field label="Location" error={errors.location}>
              <input
                value={form.location}
                onChange={(e) => update("location", e.target.value)}
                placeholder="e.g. Deira, Dubai"
                className={inputCls(errors.location)}
              />
            </Field>
            <Field label="Compensation (optional)">
              <div className="flex gap-2">
                <input
                  value={form.pay}
                  onChange={(e) => update("pay", e.target.value)}
                  placeholder="e.g. AED 4,000-5,000"
                  className={`${inputCls()} flex-1`}
                />
                <select
                  value={form.payPeriod}
                  onChange={(e) => update("payPeriod", e.target.value)}
                  className={`${inputCls()} w-32 shrink-0`}
                  aria-label="Pay period"
                >
                  <option value="per month">/ month</option>
                  <option value="per hour">/ hour</option>
                  <option value="per day">/ day</option>
                  <option value="per week">/ week</option>
                  <option value="per service">/ service</option>
                  <option value="per project">/ project</option>
                </select>
              </div>
            </Field>
          </div>

          <Field label="Description" error={errors.description}>
            <textarea
              value={form.description}
              onChange={(e) => update("description", e.target.value)}
              rows={5}
              placeholder="Describe the role, responsibilities, and what makes it a good fit…"
              className={inputCls(errors.description)}
            />
          </Field>

          <Field label="Requirements (one per line, optional)">
            <textarea
              value={form.requirements}
              onChange={(e) => update("requirements", e.target.value)}
              rows={3}
              placeholder={"Strong communication skills\nAvailable on weekends"}
              className={inputCls()}
            />
          </Field>

          <Field label="How do you know about this opening? (optional)">
            <textarea
              value={form.sharedNote}
              onChange={(e) => update("sharedNote", e.target.value)}
              rows={2}
              placeholder="e.g. I work here and we're hiring, happy to refer a fellow member."
              className={inputCls()}
            />
          </Field>

          <Field label="Contact email" error={errors.contactEmail}>
            <input
              value={form.contactEmail}
              onChange={(e) => update("contactEmail", e.target.value)}
              placeholder="e.g. you@example.com"
              className={inputCls(errors.contactEmail)}
            />
          </Field>

          <div className="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <button
              type="button"
              onClick={onCancel}
              className="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700"
            >
              Publish posting
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

function Field({ label, error, children }) {
  return (
    <label className="block">
      <span className="mb-1.5 block text-sm font-medium text-stone-700">{label}</span>
      {children}
      {error && <span className="mt-1 block text-xs text-red-600">{error}</span>}
    </label>
  )
}

function inputCls(error) {
  return `w-full rounded-lg border bg-white py-2.5 px-3 text-sm outline-none transition focus:ring-2 ${
    error
      ? "border-red-400 focus:border-red-500 focus:ring-red-200"
      : "border-stone-300 focus:border-brand-500 focus:ring-brand-200"
  }`
}
