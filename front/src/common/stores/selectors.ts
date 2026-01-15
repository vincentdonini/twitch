import {RootState} from "@stores/stores";

export const selectActiveSubscriptions = (state: RootState) =>
    state.activeSubscriptions.usernames;
