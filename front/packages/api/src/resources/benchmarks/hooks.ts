"use client";

import { useQuery } from "../../hooks/useQuery";
import type { Benchmark } from "./types";

export function useGetBenchmarks(params: Record<string, string> = {}) {
    return useQuery<Benchmark[]>("/benchmarks", params);
}

export function useGetBenchmark(id: string) {
    return useQuery<Benchmark>(`/benchmarks/${id}`);
}
