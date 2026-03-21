"use client"

import { useQuery } from "../../hooks/useQuery"
import type { User } from "./types"

export function useGetUsers(params: Record<string, string> = {}) {
  return useQuery<User[]>("/users", params)
}

export function useGetUser(id: string) {
  return useQuery<User>(`/users/${id}`)
}
