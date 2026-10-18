"use client";

import { useMutation } from "../../hooks/useMutation"
import { useQuery } from "../../hooks/useQuery";
import type { Benchmark, BenchmarkDetail, BenchmarkScore, CreateBenchmarkScorePayload } from "./types"

export function useGetBenchmarks(params: Record<string, string> = {}) {
    return useQuery<Benchmark[]>("/benchmarks", params);
}

export function useGetBenchmark(id: string) {
    return useQuery<BenchmarkDetail>(`/benchmarks/${id}`);
}

export function useCreateBenchmarkScore() {
  return useMutation<{ id: string }, CreateBenchmarkScorePayload>("/benchmark-scores", "POST")
}

export function useGetMyBenchmarkScores(params: Record<string, string | string[]> = {}) {
  return useQuery<BenchmarkScore[]>("/users/me/benchmark-scores", params)
}
