import { ITmiMessage } from "./ITmiMessage";

export interface ITmiMessageStore {
    addMessage(message: ITmiMessage): void;
    getMessages(): ITmiMessage[];
}