import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {manugraphCommands} from "@modules/tmi/domain/commands/viewer/manugraphCommands";
import {deltekCommands} from "@modules/tmi/domain/commands/viewer/deltekCommands";
import {wimjaCommands} from "@modules/tmi/domain/commands/viewer/wimjaCommands";
import {theledaxCommands} from "@modules/tmi/domain/commands/viewer/theledaxCommands";

export const viewerCommands: Record<string, CommandDefinition> = {
    ...manugraphCommands,
    ...deltekCommands,
    ...wimjaCommands,
    ...theledaxCommands,
};
