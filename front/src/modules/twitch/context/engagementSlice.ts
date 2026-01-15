import {createSlice, PayloadAction} from "@reduxjs/toolkit";

interface EngagementState {
    lastSub: string | null;
    topDonor: string | null;
    lastDonor: string | null;
    lastFollow: string | null;
}

const initialState: EngagementState = {
    lastSub: null,
    topDonor: null,
    lastDonor: null,
    lastFollow: null,
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
