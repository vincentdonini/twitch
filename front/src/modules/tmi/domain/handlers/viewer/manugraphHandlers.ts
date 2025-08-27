import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {secondsToMilliseconds} from "@common/utils/timeUtils";
import {handleEmoteCommand} from "@modules/tmi/domain/utils/handleEmoteCommand";

export const handleTriste = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "!triste",
        sounds: [
            {
                url: '/sounds/emotes/manugraph/un-peu-triste.mp3',
                duration: secondsToMilliseconds(4),
                message: `Tu es triste ? Arrête.`,
            },
        ],
    });

export const handleILoveYou = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "<3",
        sounds: [
            {
                url: msg.username === 'manugraph'
                    ? '/sounds/emotes/manugraph/I-love-you.mp3'
                    : '/sounds/emotes/manugraph/I-love-you-too.mp3',
                duration: secondsToMilliseconds(2),
                message: ``,
            },
        ],
    });
