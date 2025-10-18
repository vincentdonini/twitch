import {fetchMuscleAreaById, fetchMuscleAreas} from "@common/api/infrastructure/MuscleAreaApi";
import {MuscleArea} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

interface FetchMuscleAreasParams {
    page?: number;
    perPage?: number;
    pagination?: boolean;
}

export class MuscleAreaService extends BaseService {
    async fetchMuscleAreas({page = 1, perPage = 10, pagination = true}: FetchMuscleAreasParams = {}): Promise<ServiceResponse<MuscleArea[]>> {
        try {
            const {data, pagination} = await fetchMuscleAreas({page, perPage});
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<MuscleArea[]>(e);
        }
    }

    async fetchMuscleAreaById(id: number): Promise<ServiceResponse<MuscleArea>> {
        try {
            const data = await fetchMuscleAreaById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<MuscleArea>(e);
        }
    }
}