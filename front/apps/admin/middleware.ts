import { NextResponse } from "next/server"
import type { NextRequest } from "next/server"

const REFRESH_TOKEN_COOKIE = "auth_refresh_token"

export function middleware(request: NextRequest) {
    const { pathname } = request.nextUrl
    const isAuthenticated = request.cookies.has(REFRESH_TOKEN_COOKIE)
    const isLoginRoute = pathname.startsWith("/login")

    if (!isAuthenticated && !isLoginRoute) {
        return NextResponse.redirect(new URL("/login", request.url))
    }

    if (isAuthenticated && isLoginRoute) {
        return NextResponse.redirect(new URL("/dashboard", request.url))
    }

    return NextResponse.next()
}

export const config = {
    matcher: ["/((?!_next/static|_next/image|favicon.ico).*)"],
}