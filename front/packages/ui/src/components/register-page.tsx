import { AuthLayout } from "@workspace/ui/components/auth-layout"
import { RegisterForm } from "@workspace/ui/components/register-form"

interface RegisterPageProps {
  onSubmit?: (data: { firstName: string; lastName: string; email: string; password: string }) => Promise<void>
  isLoading?: boolean
  error?: string
}

export function RegisterPage({ onSubmit, isLoading, error }: RegisterPageProps) {
  return (
    <AuthLayout>
      <RegisterForm onSubmit={onSubmit} isLoading={isLoading} error={error} loginHref="/login" />
    </AuthLayout>
  )
}
