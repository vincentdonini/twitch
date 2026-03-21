import type { MuscleArea } from "../muscle-areas/types"

export type MuscleGroup = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    area: MuscleArea;
};

export type MuscleGroupSummary = {
    id: string;
    slug: string;
    title: string;
};
