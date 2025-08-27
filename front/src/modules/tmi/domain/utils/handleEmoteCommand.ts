import {CooldownCheck} from "@modules/tmi/domain/types/cooldownTypes";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {minutesToMilliseconds} from "@common/utils/timeUtils";
import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {CooldownMaps} from "@modules/tmi/infrastructure/cooldowns/CooldownMaps";
import {handleCooldowns} from "@modules/tmi/domain/utils/handleCooldowns";
import {queueSound} from "@modules/tmi/application/commands/queueSound";
import {store} from "@stores/stores";

type SoundData = {
    url: string;
    duration: number;
    message: string;
};

export const handleEmoteCommand = async (
    {
        msg,
        commandKey,
        sounds,
        userCooldown = false,
        commandCooldown = false,
    }: {
        msg: ITwitchMessage;
        commandKey: string;
        sounds: SoundData[];
        userCooldown?: number | false;
        commandCooldown?: number | false;
    }) => {
    const username = msg.username!;

    const cooldowns: CooldownCheck[] = [];

    if (userCooldown !== false) {
        cooldowns.push({
            key: `${commandKey}:${username}`,
            duration: minutesToMilliseconds(userCooldown),
            map: CooldownMaps.userCooldowns,
            type: "user",
        });
    }

    if (commandCooldown !== false) {
        cooldowns.push({
            key: commandKey,
            duration: minutesToMilliseconds(commandCooldown),
            map: CooldownMaps.commandCooldowns,
            type: "command",
        });
    }

    const isOnCooldown = cooldowns.length > 0
        ? await handleCooldowns(commandKey, username, cooldowns)
        : false;

    if (isOnCooldown) return;

    const selectedSound =
        sounds.length === 1 ? sounds[0] : sounds[Math.floor(Math.random() * sounds.length)];

    await twitchBot
        .sendMessage(selectedSound.message)
        .then(() => console.log(selectedSound.message));

    queueSound(store.dispatch, {
        url: selectedSound.url,
        duration: selectedSound.duration,
        volume: 1,
        timestamp: Date.now(),
    });
};