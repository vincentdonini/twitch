export { apiFetch, apiFetchWithMeta } from "./client"
export type { PaginationMeta } from "./client"

export { useQuery } from "./hooks/useQuery"
export type { UseQueryResult } from "./hooks/useQuery"

export { useMutation } from "./hooks/useMutation"
export type { UseMutationResult } from "./hooks/useMutation"

// AUTH
// ---------------------------------------------------------------------------------------------------------------------
export { getAccessToken, getRefreshToken, saveTokens, clearTokens } from "./storage"
export { AuthProvider, useAuth } from "./context/AuthProvider"
export { login, logout, register } from "./auth"
export type { LoginPayload, AuthTokens, RegisterPayload } from "./types"

// STATS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetStats } from "./resources/stats"
export type { AppStats } from "./resources/stats"

// ACHIEVEMENTS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetAchievements, useGetAchievement } from "./resources/achievements"
export type {
  Achievement,
  AchievementCategory,
  AchievementGroup,
  AchievementLevel,
  CreateAchievementPayload,
  UpdateAchievementPayload,
} from "./resources/achievements"
export { ACHIEVEMENT_LEVEL, AchievementRarity } from "./resources/achievements"

// BENCHMARKS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetBenchmarks, useGetBenchmark } from "./resources/benchmarks"
export type { Benchmark, CreateBenchmarkPayload, UpdateBenchmarkPayload } from "./resources/benchmarks"

// EQUIPMENTS
// ---------------------------------------------------------------------------------------------------------------------
export { useGetEquipments, useGetEquipment } from "./resources/equipments"
export type {
  Equipment, EquipmentSummary, CreateEquipmentPayload, UpdateEquipmentPayload,
} from "./resources/equipments"

// EXERCISES
// ---------------------------------------------------------------------------------------------------------------------
export {
  useGetExercises, useGetExercise, useGetExerciseCategories, useGetExerciseCategory,
} from "./resources/exercises"
export type {
  Exercise,
  ExerciseSummary,
  ExerciseCategory,
  ExerciseCategorySummary,
  CreateExercisePayload,
  UpdateExercisePayload,
} from "./resources/exercises"

// MUSCLES
// ---------------------------------------------------------------------------------------------------------------------
export {
  useGetMuscles, useGetMuscle,
  useGetMuscleGroups, useGetMuscleGroup,
  useGetMuscleAreas, useGetMuscleArea,
  useGetMuscleSegments, useGetMuscleSegment,
} from "./resources/muscles"
export type {
  Muscle,
  MuscleSummary,
  MuscleGroup,
  MuscleGroupSummary,
  MuscleArea,
  MuscleAreaSummary,
  MuscleSegment,
  MuscleSegmentSummary,
  CreateMusclePayload,
  UpdateMusclePayload,
} from "./resources/muscles"

// WODS
// ---------------------------------------------------------------------------------------------------------------------
export {
  Gender,
  MetricType,
  useGetWods,
  useGetWod,
  useGetWodLeaderboard,
  useCreateWodScore,
  useCreateWod,
  useUpdateWod,
  useCreateWodVariant,
  useUpdateWodVariant,
  useDeleteWodVariant,
  useGetWodAgeRanges,
  useGetWodCategories,
  useGetWodTypes,
  useGetWodDivisions,
} from "./resources/wods"
export type {
  MetricTypeEnum,
  Wod,
  WodDetail,
  WodCategory,
  WodType,
  WodDivision,
  AgeRange,
  WodVariant,
  WodExercise,
  ExerciseMetric,
  LeaderboardEntry,
  LeaderboardUser,
  CreateWodPayload,
  UpdateWodPayload,
  CreateWodScorePayload,
  CreateWodVariantPayload,
  UpdateWodVariantPayload,
  ExerciseInput,
  ExerciseMetricInput,
} from "./resources/wods"

// SUBSCRIPTIONS
// ---------------------------------------------------------------------------------------------------------------------
export {
  SubscriptionStatus,
  useGetPlaceSubscriptions,
  useGetPlanSubscriptions,
  useCreateSubscription,
} from "./resources/subscriptions"
export type { Subscription, CreateSubscriptionPayload } from "./resources/subscriptions"

// PLANS
// ---------------------------------------------------------------------------------------------------------------------
export {
  PlanStatus,
  PlanType,
  PlanBillingPeriod,
  PlanCurrency,
  useGetPlacePlans,
  useCreatePlan,
  useUpdatePlan,
  useDeletePlan,
} from "./resources/plans"
export type { Plan, CreatePlanPayload, UpdatePlanPayload } from "./resources/plans"

// USERS
// ---------------------------------------------------------------------------------------------------------------------
export {
  useGetUsers,
  useGetUser,
  useSearchUsers,
  useGetPlaceAthletes,
  useCreateUser,
  useGetPlaceCoaches,
  useAddPlaceCoach,
  useRemovePlaceCoach,
  useUpdateMe,
  useUpdatePassword,
} from "./resources/users"
export type {
  User,
  UserMe,
  UserMeGymSubscription,
  UserMePlace,
  UserMeCompany,
  CreateUserPayload,
  UpdateUserPayload,
  UpdateMePayload,
  UpdatePasswordPayload,
} from "./resources/users"
