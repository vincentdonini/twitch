import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

const PUBLIC_PATHS = ['/sign-in', '/sign-up', '/forgot-password', '/landing', '/errors']

// Use the refresh token for the guard (30 days) — same as web's middleware.
// The access token (auth_token) lasts only 1 day; checking it would force logout
// even though AuthProvider can silently refresh via auth_refresh_token.
const REFRESH_TOKEN_COOKIE = 'auth_refresh_token'

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl

  // Redirect /login to /sign-in
  if (pathname === '/login') {
    return NextResponse.redirect(new URL('/sign-in', request.url))
  }

  // Redirect /register to /sign-up
  if (pathname === '/register') {
    return NextResponse.redirect(new URL('/sign-up', request.url))
  }

  // Auth guard: redirect to sign-in if no session and not on a public path
  const isPublic = PUBLIC_PATHS.some((p) => pathname.startsWith(p))
  if (!isPublic) {
    const hasSession = request.cookies.has(REFRESH_TOKEN_COOKIE)
    if (!hasSession) {
      return NextResponse.redirect(new URL('/sign-in', request.url))
    }
  }

  return NextResponse.next()
}

export const config = {
  matcher: [
    '/((?!api|_next/static|_next/image|favicon.ico).*)',
  ],
}
