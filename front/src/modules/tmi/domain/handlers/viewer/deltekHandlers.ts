import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {secondsToMilliseconds} from "@common/utils/timeUtils";
import {handleEmoteCommand} from "@modules/tmi/domain/utils/handleEmoteCommand";

export const handlePokPikachu = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "PokPikachu",
        sounds: [
            {
                url: '/sounds/emotes/deltek/PokPikachu.mp3',
                duration: secondsToMilliseconds(7),
                message: `Il aime beaucoup trop les Pokemons ce garçon !`,
            },
        ],
    });