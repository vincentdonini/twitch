export interface ITwitchMessage {
    id?: string | undefined;
    username?: string | undefined;
    channel: string;
    message: string;
}
