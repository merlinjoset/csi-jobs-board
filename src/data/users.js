// Demo accounts for the prototype. In production these would be real
// congregation members authenticated against a backend + hashed passwords.
// Passwords are shown on the login screen on purpose so the prototype is
// easy to try. Never do this in a real app.

export const DEMO_USERS = [
  {
    id: "u-admin",
    name: "Rev. Samuel Raj",
    email: "admin@csitamilparishdubai.com",
    password: "admin123",
    role: "admin",
  },
  {
    id: "u-member",
    name: "Sarah Mitchell",
    email: "member@csitamilparishdubai.com",
    password: "member123",
    role: "member",
  },
]
