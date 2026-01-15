import {Api} from "@common/api/infrastructure/Api";
import {UserIdentifier} from "@common/api/domain/UserIdentifier";

export async function getTopDonor(): Promise<UserIdentifier> {
    return await Api.getTopDonor();
}
