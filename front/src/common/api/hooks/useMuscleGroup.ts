import {useQuery} from "@tanstack/react-query";
import {MuscleGroupService} from "@common/api/application/MuscleGroupService";
import {MuscleGroup} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscleGroup(id?: number) {
    return useQuery<ServiceResponse<MuscleGroup>>({
        queryKey: ["muscleGroup", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid muscleGroup ID");
            const service = new MuscleGroupService();
            return service.fetchMuscleGroupById(id);
        },
        enabled: !!id,
    });
}
