import {createSlice, PayloadAction} from "@reduxjs/toolkit";

interface ActiveSubscriptionsState {
    usernames: string[];
}

const initialState: ActiveSubscriptionsState = {
    usernames: [],
};

const activeSubscriptionsSlice = createSlice({
    name: "activeSubscriptions",
    initialState,
    reducers: {
        setActiveSubscriptions: (state, action: PayloadAction<string[]>) => {
            state.usernames = action.payload.map(username => username.toLowerCase());
        },
        clearActiveSubscriptions: (state) => {
            state.usernames = [];
        },
    },
});

export const {
    setActiveSubscriptions,
    clearActiveSubscriptions,
} = activeSubscriptionsSlice.actions;

export default activeSubscriptionsSlice.reducer;
