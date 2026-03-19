import { AuthLayout } from "@workspace/ui/components/auth-layout"
import { LoginForm } from "@workspace/ui/components/login-form"

interface LoginPageProps {
  onSubmit?: (email: string, password: string) => Promise<void>
  isLoading?: boolean
  error?: string
}

export function LoginPage({ onSubmit, isLoading, error }: LoginPageProps) {
  return (
    <AuthLayout>
      <LoginForm onSubmit={onSubmit} isLoading={isLoading} error={error} registerHref="/register" />
    </AuthLayout>
  )
}