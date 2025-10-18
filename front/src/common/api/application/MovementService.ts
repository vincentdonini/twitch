import {fetchMovementById, fetchMovements} from "@common/api/infrastructure/MovementApi";
import {Movement} from "@common/api/domain/Movement";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

interface FetchMovementsParams {
    page?: number;
    perPage?: number;
    pagination?: boolean;
}

export class MovementService extends BaseService {
    async fetchMovements({page = 1, perPage = 10, pagination = true}: FetchMovementsParams = {}): Promise<ServiceResponse<Movement[]>> {
        try {
            const {data, pagination} = await fetchMovements({page, perPage});
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<Movement[]>(e);
        }
    }

    async fetchMovementById(id: number): Promise<ServiceResponse<Movement>> {
        try {
            const data = await fetchMovementById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<Movement>(e);
        }
    }
}