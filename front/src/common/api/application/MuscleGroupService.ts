import {fetchMuscleGroupById, fetchMuscleGroups} from "@common/api/infrastructure/MuscleGroupApi";
import {MuscleGroup} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

interface FetchMuscleGroupsParams {
    page?: number;
    perPage?: number;
    pagination?: boolean;
}

export class MuscleGroupService extends BaseService {
    async fetchMuscleGroups({page = 1, perPage = 10, pagination = true}: FetchMuscleGroupsParams = {}): Promise<ServiceResponse<MuscleGroup[]>> {
        try {
            const {data, pagination} = await fetchMuscleGroups({page, perPage});
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<MuscleGroup[]>(e);
        }
    }

    async fetchMuscleGroupById(id: number): Promise<ServiceResponse<MuscleGroup>> {
        try {
            const data = await fetchMuscleGroupById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<MuscleGroup>(e);
        }
    }
}