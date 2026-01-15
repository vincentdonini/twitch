import {createSlice, PayloadAction} from "@reduxjs/toolkit";
import {ITmiMessage} from "@modules/tmi/domain/interfaces/ITmiMessage";
import {ITmiMessageStore} from "@modules/tmi/domain/interfaces/ITmiMessageStore";

interface MessageState {
    tmiMessages: ITmiMessage[];
}

const initialState: MessageState = {
    tmiMessages: [],
};

const messageSlice = createSlice({
    name: "messages",
    initialState,
    reducers: {
        addTmiMessage: (state, action: PayloadAction<ITmiMessage>) => {
            state.tmiMessages.push(action.payload);
        },
    },
});

export const {addTmiMessage} = messageSlice.actions;
export default messageSlice.reducer;

export class ReduxTmiMessageStore implements ITmiMessageStore {
    private messages: ITmiMessage[] = [];

    addMessage(message: ITmiMessage): void {
        this.messages.push(message);
    }

    getMessages(): ITmiMessage[] {
        return this.messages;
    }
}
