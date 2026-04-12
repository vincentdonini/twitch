"use client"

import { useQuery } from "../../hooks/useQuery"
import { useMutation } from "../../hooks/useMutation"
import type { User, UserMe, CreateUserPayload, UpdateMePayload, UpdatePasswordPayload } from "./types"

export function useGetUsers(params: Record<string, string> = {}) {
  return useQuery<User[]>("/users", params)
}

export function useSearchUsers(search: string) {
  return useQuery<User[]>("/users", search.trim() ? { search: search.trim(), limit: "10" } : {})
}

export function useGetUser(id: string) {
  return useQuery<User>(`/users/${id}`)
}

export function useGetPlaceAthletes(placeId: string) {
  return useQuery<User[]>(`/places/${placeId}/athletes`)
}

export function useGetPlaceCoaches(placeId: string) {
  return useQuery<User[]>(`/places/${placeId}/coaches`)
}

export function useAddPlaceCoach(placeId: string) {
  return useMutation<User, { email: string }>(`/places/${placeId}/coaches`, "POST")
}

export function useRemovePlaceCoach(placeId: string, userId: string) {
  return useMutation<void, void>(`/places/${placeId}/coaches/${userId}`, "DELETE")
}

export function useCreateUser() {
  return useMutation<{ id: string; message: string }, CreateUserPayload>("/auth/register", "POST", false)
}

export function useUpdateMe() {
  return useMutation<UserMe, UpdateMePayload>("/users/me", "PATCH")
}

export function useUpdatePassword() {
  return useMutation<void, UpdatePasswordPayload>("/users/me/password", "PUT")
}
