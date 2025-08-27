import {CooldownCheck} from "@modules/tmi/domain/types/cooldownTypes";
import {checkCooldown, setCooldown} from "@modules/tmi/application/services/cooldownService";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {millisecondsToSeconds} from "@common/utils/timeUtils";

export const handleCooldowns = async (
    commandKey: string,
    username: string,
    cooldowns: CooldownCheck[]
): Promise<boolean> => {
    for (const { key, duration, map, type } of cooldowns) {
        if (typeof duration !== "number" || duration <= 0) continue;

        const result = checkCooldown(key, duration, map);

        if (result.onCooldown) {
            const message =
                type === "user"
                    ? `[Cooldown] ${username}, encore ${millisecondsToSeconds(result.remaining)} seconde${result.remaining > 1 ? "s" : ""} à attendre pour "${commandKey}".`
                    : `[Cooldown] La commande "${commandKey}" est encore en cooldown pour ${result.remaining} seconde${result.remaining > 1 ? "s" : ""}.`;

            await twitchBot
                .sendMessage(message)
                .then(() => console.log(message));

            return true;
        }
    }

    cooldowns.forEach(({ key, duration, map }) => {
        if (typeof duration === "number" && duration > 0) {
            setCooldown(key, map);
        }
    });

    return false;
};
