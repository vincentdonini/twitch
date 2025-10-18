import {useInfiniteQuery, InfiniteData} from "@tanstack/react-query";
import {MuscleService} from "@common/api/application/MuscleService";
import {Muscle} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscles({perPage = 10} = {}) {
    const service = new MuscleService();

    return useInfiniteQuery<
        ServiceResponse<Muscle[]>,
        Error,
        InfiniteData<ServiceResponse<Muscle[]>>,
        string[],
        number
    >({
        queryKey: ["musclesList"],
        queryFn: async ({pageParam = 1}) => service.fetchMuscles({page: pageParam, perPage}),
        getNextPageParam: (lastPage) => {
            if (!lastPage.pagination) return undefined;
            const currentPage = lastPage.pagination.page;
            const totalPages = Math.ceil(lastPage.pagination.count / lastPage.pagination.perPage);
            return currentPage < totalPages ? currentPage + 1 : undefined;
        },
        initialPageParam: 1,
    });
}
