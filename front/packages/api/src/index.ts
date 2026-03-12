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
export {login, logout} from "./auth";
export type {LoginPayload, AuthTokens} from "./types";

// ACHIEVEMENTS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetAchievements} from "./resources/achievements/useGetAchievements";
export {useGetAchievement} from "./resources/achievements/useGetAchievement";
export type {Achievement, CreateAchievementPayload, UpdateAchievementPayload} from "./resources/achievements/types";

// EQUIPMENTS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetEquipments} from "./resources/equipments/useGetEquipments";
export {useGetEquipment} from "./resources/equipments/useGetEquipment";
export type {Equipment, CreateEquipmentPayload, UpdateEquipmentPayload} from "./resources/equipments/types";

// EXERCISES
// ---------------------------------------------------------------------------------------------------------------------
export {useGetExercises} from "./resources/exercises/useGetExercises";
export {useGetExercise} from "./resources/exercises/useGetExercise";
export type {Exercise, CreateExercisePayload, UpdateExercisePayload} from "./resources/exercises/types";

// MUSCLES
// ---------------------------------------------------------------------------------------------------------------------
export {useGetMuscles} from "./resources/muscles/muscles/useGetMuscles";
export {useGetMuscle} from "./resources/muscles/muscles/useGetMuscle";
export type {Muscle, CreateMusclePayload, UpdateMusclePayload} from "./resources/muscles/muscles/types";

export {useGetMuscleAreas} from "./resources/muscles/muscle-areas/useGetMuscleAreas";
export {useGetMuscleArea} from "./resources/muscles/muscle-areas/useGetMuscleArea";
export type {MuscleArea} from "./resources/muscles/muscle-areas/types";

export {useGetMuscleGroups} from "./resources/muscles/muscle-groups/useGetMuscleGroups";
export {useGetMuscleGroup} from "./resources/muscles/muscle-groups/useGetMuscleGroup";
export type {MuscleGroup} from "./resources/muscles/muscle-groups/types";

// WODS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetWods} from "./resources/wods/useGetWods";
export {useGetWod} from "./resources/wods/useGetWod";
export {useGetWodCategories} from "./resources/wods/wod-categories/useGetWodCategories";
export {useGetWodTypes} from "./resources/wods/wod-types/useGetWodTypes";
export {useGetWodDivisions} from "./resources/wods/wod-divisions/useGetWodDivisions";
export type {Wod, WodDetail, WodCategory, WodType, WodDivision, WodVariant, WodExercise, ExerciseMetric, MetricType} from "./resources/wods/types";

// USERS
// ---------------------------------------------------------------------------------------------------------------------
export {useGetUsers} from "./resources/users/useGetUsers";
export {useGetUser} from "./resources/users/useGetUser";
export type {User, UserMe, CreateUserPayload, UpdateUserPayload} from "./resources/users/types";
