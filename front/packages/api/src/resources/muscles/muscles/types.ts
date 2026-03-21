import type { MuscleArea } from "../muscle-areas/types"
import type { MuscleGroup } from "../muscle-groups/types"

export type Muscle = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    area: MuscleArea;
    group: MuscleGroup;
};

export type MuscleSummary = {
    id: string;
    slug: string;
    title: string;
};

export type CreateMusclePayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateMusclePayload = CreateMusclePayload;
