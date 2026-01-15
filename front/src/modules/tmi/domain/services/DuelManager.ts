import {secondsToMilliseconds} from "@common/utils/timeUtils";

type Duel = {
    challenger: string;
    opponent: string;
    timeout: NodeJS.Timeout;
};

const activeDuels = new Map<string, Duel>();

export const createDuel = (challenger: string, opponent: string, onTimeout: () => void) => {
    const key = `${challenger}-${opponent}`;
    if (activeDuels.has(key)) return false;

    const timeout = setTimeout(() => {
        activeDuels.delete(key);
        onTimeout();
    }, secondsToMilliseconds(60));

    activeDuels.set(key, {challenger, opponent, timeout});
    return true;
};

export const acceptDuel = (opponent: string) => {
    const duelEntry = Object.values(activeDuels).find((d) => d.opponent === opponent);

    if (!duelEntry) return null;

    clearTimeout(duelEntry.timeout);
    activeDuels.delete(`${duelEntry.challenger}-${duelEntry.opponent}`);
    return duelEntry;
};

export function denyDuel(opponent: string): { challenger: string; opponent: string } | null {
    const duelEntry = Object.values(activeDuels).find(d => d.opponent === opponent);
    if (!duelEntry) return null;

    // Delete duel
    activeDuels.delete(`${duelEntry.challenger}-${duelEntry.opponent}`);
    return duelEntry;
}