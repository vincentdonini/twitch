import {CooldownMap} from "@modules/tmi/domain/types/cooldownTypes";

export function checkCooldown(key: string, duration: number, map: CooldownMap) {
    const now = Date.now();
    const lastUsed = map[key] || 0;
    const remaining = Math.ceil((duration - (now - lastUsed)) / 1000);

    console.log({
        onCooldown: now - lastUsed < duration,
        remaining: Math.max(0, remaining),
    });
    return {
        onCooldown: now - lastUsed < duration,
        remaining: Math.max(0, remaining),
    };
}

export function setCooldown(key: string, map: CooldownMap) {
    map[key] = Date.now();
}
