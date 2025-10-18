import {fetchWodById, fetchWods} from "@common/api/infrastructure/WodApi";
import {Wod, WodParams} from "@common/api/domain/Wod";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

export class WodService extends BaseService {
    async fetchWods(params?: WodParams): Promise<ServiceResponse<Wod[]>> {
        try {
            const {data, pagination} = await fetchWods(params);
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<Wod[]>(e);
        }
    }

    async fetchWodById(id: number): Promise<ServiceResponse<Wod>> {
        try {
            const data = await fetchWodById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<Wod>(e);
        }
    }
}