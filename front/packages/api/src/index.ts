export {apiFetch, apiFetchWithMeta} from "./client";
export type {PaginationMeta} from "./client";

export {useQuery} from "./hooks/useQuery";
export type {UseQueryResult} from "./hooks/useQuery";

export {useMutation} from "./hooks/useMutation";
export type {UseMutationResult} from "./hooks/useMutation";

// AUTH
// ---------------------------------------------------------------------------------------------------------------------
export {getAccessToken, getRefreshToken, saveTokens, clearTokens} from "./storage";
export {AuthProvider, useAuth} from "./context/AuthProvider";
export {login, logout, register} from "./auth";
export type {LoginPayload, AuthTokens, RegisterPayload} from "./types";

// ACHIEVEMENTS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetAchievements, useGetAchievement } from "./resources/achievements";
export type { Achievement, AchievementCategory, AchievementGroup, AchievementLevel, CreateAchievementPayload, UpdateAchievementPayload } from "./resources/achievements";
export { AchievementLevelEnum, AchievementRarityEnum } from "./resources/achievements";

// BENCHMARKS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetBenchmarks, useGetBenchmark } from "./resources/benchmarks";
export type { Benchmark, CreateBenchmarkPayload, UpdateBenchmarkPayload } from "./resources/benchmarks";

// EQUIPMENTS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetEquipments, useGetEquipment } from "./resources/equipments";
export type { Equipment, EquipmentSummary, CreateEquipmentPayload, UpdateEquipmentPayload } from "./resources/equipments";

// EXERCISES
// ---------------------------------------------------------------------------------------------------------------------
export { useGetExercises, useGetExercise, useGetExerciseCategories, useGetExerciseCategory } from "./resources/exercises";
export type { Exercise, ExerciseSummary, ExerciseCategory, ExerciseCategorySummary, CreateExercisePayload, UpdateExercisePayload } from "./resources/exercises";

// MUSCLES
// ---------------------------------------------------------------------------------------------------------------------
export { useGetMuscles, useGetMuscle, useGetMuscleGroups, useGetMuscleGroup, useGetMuscleAreas, useGetMuscleArea } from "./resources/muscles";
export type { Muscle, MuscleSummary, MuscleGroup, MuscleGroupSummary, MuscleArea, MuscleAreaSummary, CreateMusclePayload, UpdateMusclePayload } from "./resources/muscles";

// WODS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetWods} from "./resources/wods/useGetWods";
export {useGetWod} from "./resources/wods/useGetWod";
export {useGetWodLeaderboard} from "./resources/wods/useGetWodLeaderboard";
export {useCreateWodScore} from "./resources/wods/useCreateWodScore";
export {useGetWodCategories} from "./resources/wods/wod-categories/useGetWodCategories";
export {useGetWodTypes} from "./resources/wods/wod-types/useGetWodTypes";
export {useGetWodDivisions} from "./resources/wods/wod-divisions/useGetWodDivisions";
export type {Wod, WodDetail, WodCategory, WodType, WodDivision, WodVariant, WodExercise, ExerciseMetric, MetricType, LeaderboardEntry, LeaderboardUser, CreateWodScorePayload} from "./resources/wods/types";
export {Gender} from "./resources/wods/types";

// USERS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetUsers} from "./resources/users/useGetUsers";
export {useGetUser} from "./resources/users/useGetUser";
export type {User, UserMe, CreateUserPayload, UpdateUserPayload} from "./resources/users/types";
