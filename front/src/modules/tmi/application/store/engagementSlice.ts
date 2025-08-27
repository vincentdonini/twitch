import {createSlice, PayloadAction} from "@reduxjs/toolkit";

interface EngagementState {
    topDonor: string | null;
    lastDonor: string | null;
    lastFollow: string | null;
    lastSub: string | null;
}

const initialState: EngagementState = {
    topDonor: null,
    lastDonor: null,
    lastFollow: null,
    lastSub: null,
};

export const engagementSlice = createSlice({
    name: "twitchEngagement",
    initialState,
    reducers: {
        setTopDonor: (state, action: PayloadAction<string>) => {
            state.topDonor = action.payload;
        },
        setLastDonor: (state, action: PayloadAction<string>) => {
            state.lastDonor = action.payload;
        },
        setLastFollow: (state, action: PayloadAction<string>) => {
            state.lastFollow = action.payload;
        },
        setLastSubscription: (state, action: PayloadAction<string>) => {
            state.lastSub = action.payload;
        },
    },
});

export const {
    setTopDonor,
    setLastDonor,
    setLastFollow,
    setLastSubscription,
} = engagementSlice.actions;

export default engagementSlice.reducer;
