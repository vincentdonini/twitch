export { useGetMuscles, useGetMuscle } from "./muscles/hooks";
export type { Muscle, MuscleSummary, CreateMusclePayload, UpdateMusclePayload } from "./muscles/types";

export { useGetMuscleGroups, useGetMuscleGroup } from "./muscle-groups/hooks";
export type { MuscleGroup, MuscleGroupSummary } from "./muscle-groups/types";

export { useGetMuscleAreas, useGetMuscleArea } from "./muscle-areas/hooks";
export type { MuscleArea, MuscleAreaSummary } from "./muscle-areas/types";

export { useGetMuscleSegments, useGetMuscleSegment } from "./muscle-segments/hooks";
export type { MuscleSegment, MuscleSegmentSummary } from "./muscle-segments/types";
