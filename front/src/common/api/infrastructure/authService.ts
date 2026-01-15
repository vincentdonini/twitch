let autoLoginCallback: (() => Promise<boolean>) | null = null;

export function setAutoLoginCallback(cb: () => Promise<boolean>) {
    autoLoginCallback = cb;
}

export async function tryAutoLogin(): Promise<boolean> {
    if (autoLoginCallback) {
        return autoLoginCallback();
    }
    return false;
}
