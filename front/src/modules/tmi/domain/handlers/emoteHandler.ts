import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {secondsToMilliseconds} from "@common/utils/timeUtils";
import {handleEmoteCommand} from "@modules/tmi/domain/utils/handleEmoteCommand";

export const handleKomodoHype = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "KomodoHype",
        sounds: [
            {
                url: '/sounds/emotes/default/komodoHype/komodoHype_01.mp3',
                duration: secondsToMilliseconds(7),
                message: `J’aime trop les dragons de Komodo aussi, mon pote !`,
            },
            {
                url: '/sounds/emotes/default/komodoHype/komodoHype_02.mp3',
                duration: secondsToMilliseconds(12),
                message: `Gloire au roi lézard !`,
            },
            {
                url: '/sounds/emotes/default/komodoHype/komodoHype_03.mp3',
                duration: secondsToMilliseconds(10),
                message: `On ne chevauche pas mes Komodos... ( enfin pas sans leur accord )`,
            },
        ],
    });

export const handleShikam3Kakas = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "shikam3Kakas",
        sounds: [
            {
                url: '/sounds/emotes/shikameow/shikam3Kakas.mp3',
                duration: secondsToMilliseconds(5),
                message: `Full-caca, full-caca !`,
            },
        ],
        userCooldown: false,
        commandCooldown: false,
    });

export const handleEntour2Seum = (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "entour2Seum",
        sounds: [
            {
                url: '/sounds/emotes/entourloupedanslazimut/entour2Seum.mp3',
                duration: secondsToMilliseconds(3),
                message: `Le seuuuuuum...`,
            },
        ],
        userCooldown: false,
        commandCooldown: false,
    });

