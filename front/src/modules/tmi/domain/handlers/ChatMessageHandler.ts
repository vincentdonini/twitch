// NOTE : domain/handlers/ — "Comment on le fait"
import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {baseCommands} from "@modules/tmi/domain/commands/baseCommands";
import {emoteCommands} from "@modules/tmi/domain/commands/emoteCommands";
import {funCommands} from "@modules/tmi/domain/commands/funCommands";
import {duelCommands} from "@modules/tmi/domain/commands/duelCommands";
import {viewerCommands} from "@modules/tmi/domain/commands/viewer";

export class CommandHandler {
    private readonly commands = {
        ...baseCommands,
        ...viewerCommands,
        ...emoteCommands,
        ...funCommands,
        ...duelCommands,
    };

    handleCommand(twitchMessage: ITwitchMessage) {
        const message = twitchMessage.message;

        const sortedCommands = Object.entries(this.commands)
            .sort(([, a], [, b]) => (a.order ?? 9999) - (b.order ?? 9999));

        for (const [key, cmd] of sortedCommands as [string, CommandDefinition][]) {
            const matches = this.matchCommand(message, key, cmd.matchType || 'strict');
            if (!matches) continue;

            if (cmd.allowedUsers && !cmd.allowedUsers.includes(twitchMessage.username!)) {
                console.log(`⛔️ ${twitchMessage.username} n'est pas autorisé à utiliser la commande "${key}"`);
                continue;
            }

            cmd.action(twitchMessage);
            return;
        }
    }

    private matchCommand(message: string, command: string, matchType: string): boolean {
        switch (matchType) {
            case 'startsWith':
                return message.startsWith(command);
            case 'endsWith':
                return message.endsWith(command);
            case 'includes':
                return message.includes(command);
            case 'strict':
                return message === command;
            case 'regex':
                try {
                    const regex = new RegExp(command);
                    return regex.test(message);
                } catch (error) {
                    console.error(`Regex error : ${command}`);
                    return false;
                }
            default:
                return false;
        }
    }
}

export const commands = new CommandHandler();