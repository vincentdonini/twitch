import {useQuery} from "@tanstack/react-query";
import {MuscleAreaService} from "@common/api/application/MuscleAreaService";
import {MuscleArea} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscleArea(id?: number) {
    return useQuery<ServiceResponse<MuscleArea>>({
        queryKey: ["muscleArea", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid muscleArea ID");
            const service = new MuscleAreaService();
            return service.fetchMuscleAreaById(id);
        },
        enabled: !!id,
    });
}
