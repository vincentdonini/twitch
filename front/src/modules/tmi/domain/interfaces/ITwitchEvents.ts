import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";

export interface IMessageReceivedEvent {
    type: "MESSAGE_RECEIVED";
    payload: ITwitchMessage;
}