export function minutesToMilliseconds(minutes: number) {
    return Math.round(minutes * 60 * 1000);
}

export function secondsToMilliseconds(seconds: number) {
    return Math.round(seconds * 1000);
}

export function millisecondsToSeconds(milliseconds: number) {
    return Math.round(milliseconds / 1000);
}
