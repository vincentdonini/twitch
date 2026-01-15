import {createSlice, PayloadAction} from "@reduxjs/toolkit";

interface ActiveMusic {
    artist: string;
    music: string;
    album: string;
    year: number;
    url: string;
}

interface SpotifyState {
    activeMusic: ActiveMusic | null;
}

const initialState: SpotifyState = {
    activeMusic: null,
};

const spotifySlice = createSlice({
    name: "spotify",
    initialState,
    reducers: {
        setActiveMusic: (state, action: PayloadAction<ActiveMusic>) => {
            state.activeMusic = action.payload;
        },
        clearActiveMusic: (state) => {
            state.activeMusic = null;
        },
    },
});

export const {
    setActiveMusic,
    clearActiveMusic,
} = spotifySlice.actions;

export default spotifySlice.reducer;
