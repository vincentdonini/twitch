import {createDuel, acceptDuel, denyDuel} from "@modules/tmi/domain/services/DuelManager";
import {queueSound} from "@modules/tmi/application/commands/queueSound";
import {store} from "@stores/stores";
import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

export const duelCommands: Record<string, CommandDefinition> = {
    "!duel": {
        matchType: "startsWith",
        action: (msg) => {
            const match = msg.message.match(/^!duel\s+@?(\w+)/);
            if (!match) return;

            const opponent = match[1];
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            if (opponent.toLowerCase() === msg.username.toLowerCase()) {
                console.log("Tu ne peux pas te défier toi-même, warrior solitaire 😅");
                twitchBot.sendMessage("Tu ne peux pas te défier toi-même, warrior solitaire 😅").then();
                return;
            }

            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            const success = createDuel(msg.username, opponent, () => {
                console.log(`⏱️ Duel entre ${msg.username} et ${opponent} annulé (temps écoulé)`);
                twitchBot.sendMessage(`⏱️ Duel entre ${msg.username} et ${opponent} annulé (temps écoulé)`).then();
            });

            if (success) {
                console.log(`⚔️ ${msg.username} défie ${opponent} en duel ! Tape !accept pour relever le défi !`);
                twitchBot.sendMessage(`⚔️ ${msg.username} défie ${opponent} en duel ! Tape !accept pour relever le défi !`);
            } else {
                console.log(`Un duel est déjà en cours entre ${msg.username} et ${opponent}`);
                twitchBot.sendMessage(`Un duel est déjà en cours entre ${msg.username} et ${opponent}`).then();
            }
        }
    },
    "!accept": {
        matchType: "strict",
        action: (msg) => {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            const duel = acceptDuel(msg.username);
            if (!duel) {
                console.log(`Aucun duel en attente pour toi, ${msg.username}`);
                twitchBot.sendMessage(`Aucun duel en attente pour toi, ${msg.username}`);
                return;
            }

            const winner = Math.random() < 0.5 ? duel.challenger : duel.opponent;
            const loser = winner === duel.challenger ? duel.opponent : duel.challenger;

            // Optionnel : jouer un son ici
            queueSound(store.dispatch, {
                url: '/sounds/duel.mp3',
                duration: 5000,
                volume: 1,
                timestamp: Date.now(),
            });

            console.log(`🏆 Duel terminé ! ${winner} a humilié ${loser} en combat singulier 😤`);
            twitchBot.sendMessage(`🏆 Duel terminé ! ${winner} a humilié ${loser} en combat singulier 😤`);
        }
    },
    "!deny": {
        matchType: "strict",
        action: (msg) => {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            const denied = denyDuel(msg.username);
            if (!denied) {
                console.log(`Aucun duel à refuser pour ${msg.username}`);
                twitchBot.sendMessage(`⚠️ Aucun duel en attente pour toi, ${msg.username}`);
                return;
            }

            console.log(`❌ ${msg.username} a refusé le duel contre ${denied.challenger}... la lâcheté 😮‍💨`);
            twitchBot.sendMessage(`❌ ${msg.username} a refusé le duel contre ${denied.challenger}... la lâcheté 😮‍💨`);
        }
    }
};
