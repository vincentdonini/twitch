"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Benchmark} from "./types";

export function useGetBenchmark(id: string) {
    return useQuery<Benchmark>(`/benchmarks/${id}`);
}
