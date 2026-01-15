import {createSlice, PayloadAction} from '@reduxjs/toolkit';
import {Events} from "tmi.js";

export interface AlertEvent {
    type: keyof Events;
    username: string;
    message: string;
    timestamp: number;
}

interface AlertQueueState {
    alerts: AlertEvent[];
    currentAlert: AlertEvent | null;
}

const initialState: AlertQueueState = {
    alerts: [],
    currentAlert: null,
};

const alertQueueSlice = createSlice({
    name: 'alertQueue',
    initialState,
    reducers: {
        addAlert: (state, action: PayloadAction<AlertEvent>) => {
            state.alerts.push(action.payload);
        },
        removeAlert: (state) => {
            console.log('removeAlert');
            state.alerts.shift();
        },
        setCurrentAlert: (state, action: PayloadAction<AlertEvent | null>) => {
            state.currentAlert = action.payload;
        },
        clearAlerts: (state) => {
            state.alerts = [];
        },
    },
});

export const {addAlert, removeAlert, setCurrentAlert, clearAlerts} = alertQueueSlice.actions;
export default alertQueueSlice.reducer;
