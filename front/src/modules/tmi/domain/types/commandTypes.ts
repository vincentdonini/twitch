import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";

export type CommandAction = (twitchMessage: ITwitchMessage) => void;

export interface CommandDefinition {
    order?: number;
    action: CommandAction;
    allowedUsers?: string[];
    matchType?: 'strict' | 'startsWith' | 'endsWith' | 'regex' | 'includes';
}
