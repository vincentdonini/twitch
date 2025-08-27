import {createSlice, PayloadAction} from '@reduxjs/toolkit';

export interface SoundEvent {
    url: string;
    duration?: number;
    volume?: number;
    timestamp: number;
}

interface SoundQueueState {
    sounds: SoundEvent[];
    currentSound: SoundEvent | null;
}

const initialState: SoundQueueState = {
    sounds: [],
    currentSound: null,
};

const soundQueueSlice = createSlice({
    name: 'soundQueue',
    initialState,
    reducers: {
        addSound: (state, action: PayloadAction<SoundEvent>) => {
            state.sounds.push(action.payload);
        },
        removeSound: (state) => {
            console.log('removeSound');
            state.sounds.shift();
        },
        setCurrentSound: (state, action: PayloadAction<SoundEvent | null>) => {
            state.currentSound = action.payload;
        },
        clearSounds: (state) => {
            state.sounds = [];
        },
    },
});

export const {addSound, removeSound, setCurrentSound, clearSounds} = soundQueueSlice.actions;
export default soundQueueSlice.reducer;
