import {Api} from "@common/api/infrastructure/Api";
import {User} from "@common/api/domain/User";

export async function getUsers(): Promise<User[]> {
    return await Api.getUsers();
}
