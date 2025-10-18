import {useInfiniteQuery, InfiniteData} from "@tanstack/react-query";
import {MuscleGroupService} from "@common/api/application/MuscleGroupService";
import {MuscleGroup} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscleGroups({perPage = 10} = {}) {
    const service = new MuscleGroupService();

    return useInfiniteQuery<
        ServiceResponse<MuscleGroup[]>,
        Error,
        InfiniteData<ServiceResponse<MuscleGroup[]>>,
        string[],
        number
    >({
        queryKey: ["muscleGroupsList"],
        queryFn: async ({pageParam = 1}) => service.fetchMuscleGroups({page: pageParam, perPage}),
        getNextPageParam: (lastPage) => {
            if (!lastPage.pagination) return undefined;
            const currentPage = lastPage.pagination.page;
            const totalPages = Math.ceil(lastPage.pagination.count / lastPage.pagination.perPage);
            return currentPage < totalPages ? currentPage + 1 : undefined;
        },
        initialPageParam: 1,
    });
}
