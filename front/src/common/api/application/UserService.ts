import {fetchMe, fetchUserById, fetchUsers} from "@common/api/infrastructure/UserApi";
import {User} from "@common/api/domain/User";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

export class UserService extends BaseService {
    async fetchCurrentUser(): Promise<ServiceResponse<User>> {
        try {
            const data = await fetchMe();
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<User>(e);
        }
    }

    async fetchUserById(id: number): Promise<ServiceResponse<User>> {
        try {
            const data = await fetchUserById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<User>(e);
        }
    }

    async fetchUsers(): Promise<ServiceResponse<User[]>> {
        try {
            const {data, pagination} = await fetchUsers();
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<User[]>(e);
        }
    }
}
