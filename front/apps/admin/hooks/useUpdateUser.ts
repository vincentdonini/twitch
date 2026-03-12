"use client";

import { useMutation } from "@workspace/api";
import type { User, UpdateUserPayload } from "@workspace/api";

export function useUpdateUser(id: string) {
    return useMutation<User, UpdateUserPayload>(`/api/users/${id}`, "PATCH");
}
