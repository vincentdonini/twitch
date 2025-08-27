import {createSlice, PayloadAction} from "@reduxjs/toolkit";
import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";

interface ChatState {
    messages: ITwitchMessage[];
}

const initialState: ChatState = {
    messages: [],
};

const chatSlice = createSlice({
    name: "chat",
    initialState,
    reducers: {
        addMessage: (state, action: PayloadAction<ITwitchMessage>) => {
            state.messages.push(action.payload);
        },
        deleteMessage: (state, action: PayloadAction<ITwitchMessage>) => {
            state.messages = state.messages.filter((msg: ITwitchMessage) => msg.id !== action.payload.id);
        },
        clearMessages: (state) => {
            state.messages = [];
        },
    },
});

export const {
    addMessage,
    deleteMessage,
    clearMessages,
} = chatSlice.actions;

export default chatSlice.reducer;
