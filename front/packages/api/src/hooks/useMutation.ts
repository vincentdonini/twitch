"use client";

import {useCallback, useState} from "react";
import {apiFetch} from "../client";

type Method = "POST" | "PUT" | "PATCH" | "DELETE";

export interface UseMutationResult<TData, TBody> {
    mutate: (body?: TBody, params?: Record<string, string>) => Promise<TData>;
    isLoading: boolean;
    error: string | null;
    reset: () => void;
}

export function useMutation<TData, TBody = unknown>(
    url: string,
    method: Method = "POST",
    auth = true
): UseMutationResult<TData, TBody> {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const mutate = useCallback(
        async (body?: TBody, params: Record<string, string> = {}): Promise<TData> => {
            setIsLoading(true);
            setError(null);

            try {
                return await apiFetch<TData>(
                    url,
                    {
                        method,
                        ...(body !== undefined && {body: JSON.stringify(body)}),
                    },
                    params,
                    auth
                );
            } catch (err: unknown) {
                const message = err instanceof Error ? err.message : "Unknown error";
                setError(message);
                throw err;
            } finally {
                setIsLoading(false);
            }
        },
        [url, method, auth]
    );

    const reset = useCallback(() => setError(null), []);

    return {mutate, isLoading, error, reset};
}
