import {Api} from "@common/api/infrastructure/Api";
import {UserIdentifier} from "@common/api/domain/UserIdentifier";

export async function getLastSubscription(): Promise<UserIdentifier> {
    return await Api.getLastSubscription();
}
