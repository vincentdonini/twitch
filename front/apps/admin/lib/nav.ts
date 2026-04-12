import { type UserMe } from "@workspace/api"
import {
  Building,
  Building2,
  CalendarDays,
  CalendarPlus,
  CreditCard,
  Crown,
  Dumbbell,
  FileText,
  LayoutDashboard,
  type LucideIcon,
  MenuSquare,
  Package,
  Receipt,
  ShieldCheck,
  TrendingUp,
  Trophy,
  UserCheck,
  UserCog,
  Users,
  Warehouse,
  Zap,
} from "lucide-react"

// ─── Types ────────────────────────────────────────────────────────────────────

export interface NavItem {
  title: string
  url: string
  icon?: LucideIcon
  badge?: string
  target?: "_blank"
  items?: { title: string; url: string; icon?: LucideIcon }[]
}

export interface NavGroup {
  label: string
  items: NavItem[]
}

// ─── Role helpers ─────────────────────────────────────────────────────────────

export const ROLES = {
  ATHLETE: "ROLE_ATHLETE",
  COACH: "ROLE_COACH",
  OWNER: "ROLE_OWNER",
  ADMIN: "ROLE_ADMIN",
} as const

export type AppRole = typeof ROLES[keyof typeof ROLES]

export function hasRole(roles: string[], role: AppRole): boolean {
  return roles.includes(role)
}

export function hasAnyRole(roles: string[], check: AppRole[]): boolean {
  return check.some((r) => roles.includes(r))
}

// ─── Gym subscription type ────────────────────────────────────────────────────

export interface GymSubscription {
  id: string
  gymName: string
}

// ─── Translation type ─────────────────────────────────────────────────────────

export type NavT = (key: string) => string

// ─── Nav definitions ──────────────────────────────────────────────────────────

function generalGroup(t: NavT): NavGroup {
  return {
    label: t("general"),
    items: [
      { title: t("dashboard"), url: "/dashboard", icon: LayoutDashboard },
      { title: t("wods"), url: "/wods", icon: Zap },
      { title: t("sessions"), url: "/sessions", icon: Dumbbell },
      { title: t("performances"), url: "/performances", icon: TrendingUp },
      { title: t("achievements"), url: "/achievements", icon: Trophy },
    ],
  }
}

function gymSubItems(gymId: string, t: NavT): { title: string; url: string; icon?: LucideIcon }[] {
  return [
    { title: t("my_gym"), url: `/gym/${gymId}`, icon: Building },
    { title: t("members"), url: `/gym/${gymId}/members`, icon: Users },
    { title: t("book_session"), url: `/gym/${gymId}/book`, icon: CalendarPlus },
    { title: t("my_subscription"), url: `/gym/${gymId}/subscription`, icon: CreditCard },
  ]
}

/**
 * Une subscription → groupe avec le nom de la salle en label, items plats.
 * Plusieurs subscriptions → groupe traduit avec un sous-menu par salle.
 */
function gymGroups(subscriptions: GymSubscription[], t: NavT): NavGroup[] {
  if (subscriptions.length === 1) {
    const { id, gymName } = subscriptions[0]
    return [
      {
        label: gymName,
        items: gymSubItems(id, t).map(({ title, url, icon }) => ({ title, url, icon })),
      },
    ]
  }

  return [
    {
      label: t("my_gyms"),
      items: subscriptions.map(({ id, gymName }) => ({
        title: gymName,
        url: "#",
        icon: Warehouse,
        items: gymSubItems(id, t),
      })),
    },
  ]
}

function coachSubItems(gymId: string, t: NavT): { title: string; url: string; icon?: LucideIcon }[] {
  return [
    { title: t("dashboard"), url: `/coach/${gymId}/dashboard`, icon: LayoutDashboard },
    { title: t("planning"), url: `/coach/${gymId}/planning`, icon: CalendarDays },
    { title: t("members"), url: `/coach/${gymId}/members`, icon: UserCog },
    { title: t("rights_plans"), url: `/coach/${gymId}/plans`, icon: FileText },
  ]
}

/**
 * Une salle → groupe "Coaching" avec items plats.
 * Plusieurs salles → groupe "Coaching" avec un sous-menu par salle.
 */
function coachGroups(gyms: GymSubscription[], t: NavT): NavGroup[] {
  if (gyms.length === 1) {
    const { id } = gyms[0]
    return [
      {
        label: t("coaching"),
        items: coachSubItems(id, t).map(({ title, url, icon }) => ({ title, url, icon })),
      },
    ]
  }

  return [
    {
      label: t("coaching"),
      items: gyms.map(({ id, gymName }) => ({
        title: gymName,
        url: "#",
        icon: Warehouse,
        items: coachSubItems(id, t),
      })),
    },
  ]
}

/**
 * Une société → groupe avec le nom de la société en label.
 * Plusieurs sociétés → un groupe par société.
 * Chaque groupe contient : item Société (paramètres) + un item par salle.
 */
function ownerGroups(
  companies: { id: string; name: string; places: { id: string; name: string }[] }[],
  t: NavT,
): NavGroup[] {
  return companies.map(({ id: companyId, name: companyName, places }) => ({
    label: companyName,
    items: [
      {
        title: t("company"),
        url: "#",
        icon: Building2,
        items: [
          { title: t("informations"), url: `/owner/${companyId}/settings/info` },
          { title: t("access_roles"), url: `/owner/${companyId}/settings/access` },
        ],
      },
      ...places.map(({ id: placeId, name: placeName }) => ({
        title: placeName,
        url: "#",
        icon: Warehouse,
        items: [
          { title: t("plans"), url: `/owner/${companyId}/places/${placeId}/plans`, icon: Package },
          { title: t("coaches"), url: `/owner/${companyId}/places/${placeId}/coaches`, icon: UserCheck },
          { title: t("billing"), url: `/owner/${companyId}/places/${placeId}/billing`, icon: Receipt },
        ],
      })),
    ],
  }))
}

/** Visible pour ROLE_ADMIN uniquement. */
function adminGroup(t: NavT): NavGroup {
  return {
    label: t("administration"),
    items: [
      { title: t("companies"), url: "/admin/companies", icon: Building2 },
      { title: t("owners"), url: "/admin/owners", icon: Crown },
      { title: t("coaches"), url: "/admin/coaches", icon: UserCheck },
      { title: t("wods"), url: "/admin/wods", icon: Dumbbell },
      { title: t("permissions"), url: "/admin/permissions", icon: ShieldCheck },
      { title: t("menus"), url: "/admin/menus", icon: MenuSquare },
    ],
  }
}


// ─── Main builder ─────────────────────────────────────────────────────────────

/**
 * Construit les groupes de navigation en fonction du rôle de l'utilisateur.
 *
 * @param user  L'utilisateur connecté (null pendant le chargement).
 * @param t     Fonction de traduction — `useTranslations("nav")` depuis le composant appelant.
 */
export function buildNav(user: UserMe | null, t: NavT): NavGroup[] {
  if (!user) return []

  const { roles } = user

  const gymSubscriptions: GymSubscription[] = (user.gymSubscriptions ?? []).map((s) => ({
    id: s.placeId,
    gymName: s.placeName,
  }))

  const coachGyms: GymSubscription[] = (user.coachPlaces ?? []).map((p) => ({
    id: p.id,
    gymName: p.name,
  }))

  const isCoach = coachGyms.length > 0
  const isOwner = (user.ownerCompanies ?? []).length > 0

  const groups: NavGroup[] = []

  // ── Accès athlète (base) ─────────────────────────────────────────────────
  groups.push(generalGroup(t))

  if (gymSubscriptions.length > 0) {
    groups.push(...gymGroups(gymSubscriptions, t))
  }

  // ── Accès coach (détecté via user_has_place) ──────────────────────────────
  if (isCoach) {
    groups.push(...coachGroups(coachGyms, t))
  }

  // ── Accès owner (détecté via user_has_company) ────────────────────────────
  if (isOwner) {
    groups.push(...ownerGroups(user.ownerCompanies ?? [], t))
  }

  // ── Accès admin global ───────────────────────────────────────────────────
  if (hasRole(roles, ROLES.ADMIN)) {
    groups.push(adminGroup(t))
  }

  // groups.push(settingsGroup())

  return groups
}
