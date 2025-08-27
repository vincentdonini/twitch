import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {secondsToMilliseconds} from "@common/utils/timeUtils";
import {handleEmoteCommand} from "@modules/tmi/domain/utils/handleEmoteCommand";

export const handleWimjaGg = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "wimjaGg",
        sounds: [
            {
                url: '/sounds/emotes/wimja/growl_01.mp3',
                duration: secondsToMilliseconds(2),
                message: `Wow, t’as réussi à aligner deux lettres... un exploit pour un cerveau en décomposition !`,
            },
            {
                url: '/sounds/emotes/wimja/growl_02.mp3',
                duration: secondsToMilliseconds(1),
                message: `C’est mignon, t’as appris à écrire autre chose que 'graaaah'.`,
            },
            {
                url: '/sounds/emotes/wimja/growl_03.mp3',
                duration: secondsToMilliseconds(2),
                message: `Merci, venant d’un mec qui confond 'GG' avec un râle d’agonie, ça touche.`,
            },
        ],
        userCooldown: false,
        commandCooldown: false,
    });

export const handleWimjaKeur = async (msg: ITwitchMessage) =>
    handleEmoteCommand({
        msg,
        commandKey: "wimjaKeur",
        sounds: [
            {
                url: '/sounds/emotes/wimja/growl_01.mp3',
                duration: secondsToMilliseconds(2),
                message: `Ton idée de l’amour, c’est partager une cervelle au coin du feu ?`,
            },
            {
                url: '/sounds/emotes/wimja/growl_02.mp3',
                duration: secondsToMilliseconds(1),
                message: `C’est touchant… enfin, façon de parler hein, vu l’état de tes doigts.`,
            },
            {
                url: '/sounds/emotes/wimja/growl_03.mp3',
                duration: secondsToMilliseconds(2),
                message: `L’amour c’est beau… surtout quand t’as pas mangé la personne en face.`,
            },
        ],
        userCooldown: false,
        commandCooldown: false,
    });