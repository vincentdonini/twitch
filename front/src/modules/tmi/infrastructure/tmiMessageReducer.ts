import {createSlice, PayloadAction} from '@reduxjs/toolkit';
import {ITmiMessage} from "@modules/tmi/domain/interfaces/ITmiMessage";

interface MessageState {
    tmiMessages: ITmiMessage[];
}

const initialState: MessageState = {
    tmiMessages: [],
};

const messageSlice = createSlice({
    name: 'messages',
    initialState,
    reducers: {
        addTmiMessage: (state, action: PayloadAction<ITmiMessage>) => {
            state.tmiMessages.push(action.payload);
        },
    },
});

export const {addTmiMessage} = messageSlice.actions;
export default messageSlice.reducer;
