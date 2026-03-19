"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { useAuth } from "@workspace/api"
import { LoginPage } from "@workspace/ui/components/login-page"

export default function Page() {
  const { loginUser } = useAuth()
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | undefined>()

  async function handleSubmit(email: string, password: string) {
    setIsLoading(true)
    setError(undefined)
    try {
      await loginUser(email, password)
      router.push("/")
    } catch {
      setError("Invalid email or password.")
    } finally {
      setIsLoading(false)
    }
  }

  return <LoginPage onSubmit={handleSubmit} isLoading={isLoading} error={error} />
}
