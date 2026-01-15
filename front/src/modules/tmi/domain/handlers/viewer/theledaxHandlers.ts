import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {secondsToMilliseconds} from "@common/utils/timeUtils";
import {handleEmoteCommand} from "@modules/tmi/domain/utils/handleEmoteCommand";

export const handleVegaMissyl = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "Vega Missyl",
        sounds: [
            {
                url: '/sounds/emotes/theledax/vega-missyl.mp3',
                duration: secondsToMilliseconds(7),
                message: `Avec Vega Missyl, vous êtes satellisés, vous êtes dans un autre monde !`,
            },
        ],
    });