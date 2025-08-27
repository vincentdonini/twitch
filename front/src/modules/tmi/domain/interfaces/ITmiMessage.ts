import {ChatUserstate} from "tmi.js";

export interface ITmiMessage {
    channel: string;
    tags: ChatUserstate;
    message: string;
    self: boolean;
}