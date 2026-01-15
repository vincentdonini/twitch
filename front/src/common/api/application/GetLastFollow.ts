import {Api} from "@common/api/infrastructure/Api";
import {UserIdentifier} from "@common/api/domain/UserIdentifier";

export async function getLastFollow(): Promise<UserIdentifier> {
    return await Api.getLastFollow();
}
