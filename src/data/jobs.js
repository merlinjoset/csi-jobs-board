// Seed data for the community job board.
//
// IMPORTANT: These are NOT the church's own staff vacancies. This board is a
// referral network: members post job openings they personally know about
// (at their workplace, their own business, or through their network) to help
// fellow members of CSI Tamil Parish Dubai find good work.
//
// In the prototype these live in-memory + localStorage; a real deployment
// would back this with a database and parish-member authentication.

// Bump SEED_VERSION whenever the seed content below changes so returning
// visitors re-seed with the latest data (their own posted jobs are kept).
export const SEED_VERSION = 3

export const CATEGORIES = [
  "Administration",
  "Childcare & Education",
  "Construction & Trades",
  "Creative & Media",
  "Food & Hospitality",
  "Healthcare",
  "Retail & Sales",
  "Logistics & Driving",
  "Technology",
  "Other",
]

export const JOB_TYPES = ["Full-time", "Part-time", "Contract", "Temporary", "Volunteer"]

export const SEED_JOBS = [
  {
    id: "seed-1",
    title: "Office Administrator / Receptionist",
    org: "Al Futtaim Group (Deira)",
    postedBy: "Anitha Prakash",
    sharedNote: "I work in this department and we have an opening, happy to refer a fellow member.",
    category: "Administration",
    type: "Full-time",
    location: "Deira, Dubai",
    pay: "AED 4,000-5,000 / month",
    postedAt: "2026-07-30",
    description:
      "A trusted employer in Deira is hiring a front-desk administrator to manage reception, handle calls and email, and support the office team. Stable, family-friendly workplace with good benefits.",
    requirements: [
      "Strong organizational and communication skills",
      "Comfortable with email, spreadsheets, and calendar tools",
      "Prior office/reception experience preferred",
    ],
    contactEmail: "anitha.prakash@example.com",
    tags: ["Office", "Referral"],
    status: "approved",
  },
  {
    id: "seed-2",
    title: "Nursery Assistant",
    org: "Little Steps Nursery, Al Barsha",
    postedBy: "Sarah Mitchell",
    sharedNote: "My children go here and they mentioned they're short-staffed.",
    category: "Childcare & Education",
    type: "Part-time",
    location: "Al Barsha, Dubai",
    pay: "AED 45-55 / hour",
    postedAt: "2026-08-01",
    description:
      "Well-regarded nursery is looking for a caring assistant to help with infants and toddlers during the mornings. A lovely environment for someone who adores little ones.",
    requirements: [
      "Experience with young children preferred",
      "Reference check required (the nursery arranges it)",
      "Patient, gentle, and dependable",
    ],
    contactEmail: "sarah.mitchell@example.com",
    tags: ["Kids", "Referral"],
    status: "approved",
  },
  {
    id: "seed-3",
    title: "Handyman / General Maintenance",
    org: "Thomas Family Services (member-owned)",
    postedBy: "Mark Thomas",
    sharedNote: "This is my own business and I'd love to hire a fellow parish member first.",
    category: "Construction & Trades",
    type: "Contract",
    location: "Greater Dubai area",
    pay: "Negotiable / per project",
    postedAt: "2026-07-28",
    description:
      "My family maintenance business is expanding and I'd rather hire from within the parish. Reliable work doing residential repairs, painting, and small remodels for the right person with a good attitude.",
    requirements: [
      "2+ years of general home-repair experience",
      "Own basic tools and reliable transportation",
      "References from previous clients",
    ],
    contactEmail: "mark.thomas@example.com",
    tags: ["Trades", "Member business"],
    status: "approved",
  },
  {
    id: "seed-4",
    title: "Junior Accountant",
    org: "Gulf Star Trading LLC",
    postedBy: "Grace Nirmal",
    sharedNote: "My employer is hiring and asked me to spread the word.",
    category: "Administration",
    type: "Full-time",
    location: "Business Bay, Dubai",
    pay: "AED 5,500-7,000 / month",
    postedAt: "2026-07-25",
    description:
      "Growing trading company needs a junior accountant to manage invoices, reconcile accounts, and support month-end reporting. Friendly team and clear path to grow.",
    requirements: [
      "Experience with Tally or QuickBooks",
      "Attention to detail and confidentiality",
      "Bachelor's in Commerce/Accounting preferred",
    ],
    contactEmail: "grace.nirmal@example.com",
    tags: ["Finance", "Referral"],
    status: "approved",
  },
  {
    id: "seed-5",
    title: "Retail Sales Associate",
    org: "Home Centre, Mall of the Emirates",
    postedBy: "Deepa Sundar",
    sharedNote: "A close friend manages this store and is hiring for the season.",
    category: "Retail & Sales",
    type: "Full-time",
    location: "Mall of the Emirates, Dubai",
    pay: "AED 3,500-4,200 / month",
    postedAt: "2026-08-02",
    description:
      "Busy home-goods store is hiring friendly sales associates to help customers, manage displays, and handle checkout. Great for someone warm and energetic. Commission on top of base salary.",
    requirements: [
      "Customer-facing or retail experience a plus",
      "Comfortable working weekends and shifts",
      "Good spoken English; Tamil/Hindi a bonus",
    ],
    contactEmail: "deepa.sundar@example.com",
    tags: ["Retail", "Referral"],
    status: "approved",
  },
  {
    id: "seed-6",
    title: "Delivery Driver (LMV Licence)",
    org: "Swift Logistics Dubai",
    postedBy: "Joseph Anand",
    sharedNote: "My cousin's company needs dependable drivers right now.",
    category: "Logistics & Driving",
    type: "Full-time",
    location: "Al Quoz, Dubai",
    pay: "AED 3,000-3,800 / month",
    postedAt: "2026-08-03",
    description:
      "Logistics firm needs reliable delivery drivers for daytime routes across Dubai. Company vehicle provided. Steady hours and on-time salary.",
    requirements: [
      "Valid UAE LMV driving licence",
      "Knowledge of Dubai routes",
      "Punctual and careful with deliveries",
    ],
    contactEmail: "joseph.anand@example.com",
    tags: ["Driving", "Referral"],
    status: "approved",
  },
]
