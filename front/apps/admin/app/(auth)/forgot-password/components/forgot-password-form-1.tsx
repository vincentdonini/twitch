"use client"

import { cn } from "@/lib/utils"
import { Button } from "@workspace/ui/components/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@workspace/ui/components/card"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"

export function ForgotPasswordForm1({
  className,
  ...props
}: React.ComponentProps<"div">) {
  return (
    <div className={cn("flex flex-col gap-6", className)} {...props}>
      <Card>
        <CardHeader className="text-center">
          <CardTitle className="text-xl">Forgot your password?</CardTitle>
          <CardDescription>
            Enter your email address and we&apos;ll send you a link to reset your password
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form>
            <div className="grid gap-6">
              <div className="grid gap-6">
                <div className="grid gap-3">
                  <Label htmlFor="email">Email</Label>
                  <Input
                    id="email"
                    type="email"
                    placeholder="m@example.com"
                    required
                  />
                </div>
                <Button type="submit" className="w-full cursor-pointer">
                  Send Reset Link
                </Button>
              </div>
              <div className="text-center text-sm">
                Remember your password?{" "}
                <a href="/auth/sign-in" className="underline underline-offset-4">
                  Back to sign in
                </a>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
