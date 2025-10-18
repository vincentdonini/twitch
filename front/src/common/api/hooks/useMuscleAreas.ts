import {useInfiniteQuery, InfiniteData} from "@tanstack/react-query";
import {MuscleAreaService} from "@common/api/application/MuscleAreaService";
import {MuscleArea} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscleAreas({perPage = 10} = {}) {
    const service = new MuscleAreaService();

    return useInfiniteQuery<
        ServiceResponse<MuscleArea[]>,
        Error,
        InfiniteData<ServiceResponse<MuscleArea[]>>,
        string[],
        number
    >({
        queryKey: ["muscleAreasList"],
        queryFn: async ({pageParam = 1}) => service.fetchMuscleAreas({page: pageParam, perPage}),
        getNextPageParam: (lastPage) => {
            if (!lastPage.pagination) return undefined;
            const currentPage = lastPage.pagination.page;
            const totalPages = Math.ceil(lastPage.pagination.count / lastPage.pagination.perPage);
            return currentPage < totalPages ? currentPage + 1 : undefined;
        },
        initialPageParam: 1,
    });
}
