"use client";

import { useMutation } from "@workspace/api";
import type { User, CreateUserPayload } from "@workspace/api";

export function useCreateUser() {
    return useMutation<User, CreateUserPayload>("/api/users", "POST");
}
