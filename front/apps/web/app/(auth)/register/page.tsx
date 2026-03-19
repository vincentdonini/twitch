"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { register } from "@workspace/api"
import { RegisterPage } from "@workspace/ui/components/register-page"

export default function Page() {
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | undefined>()

  async function handleSubmit(data: { firstName: string; lastName: string; email: string; password: string }) {
    setIsLoading(true)
    setError(undefined)
    try {
      await register(data)
      router.push("/login")
    } catch {
      setError("An error occurred. Please try again.")
    } finally {
      setIsLoading(false)
    }
  }

  return <RegisterPage onSubmit={handleSubmit} isLoading={isLoading} error={error} />
}
