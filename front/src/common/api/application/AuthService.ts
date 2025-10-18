import { loginApi, LoginResponse, refreshTokenApi } from '@common/api/infrastructure/ApiAuth';

export class AuthService {
    private tokenKey: string;
    private refreshTokenKey: string;

    constructor(tokenKey: string = 'token', refreshTokenKey: string = 'refresh_token') {
        this.tokenKey = tokenKey;
        this.refreshTokenKey = refreshTokenKey;
    }

    async login(email: string, password: string): Promise<LoginResponse> {
        const res = await loginApi(email, password);

        localStorage.setItem(this.tokenKey, res.token);
        if (res.refresh_token) {
            localStorage.setItem(this.refreshTokenKey, res.refresh_token);
        }

        return res;
    }

    logout() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.refreshTokenKey);
    }

    async refreshToken(): Promise<string | null> {
        const refreshToken = this.getRefreshToken();
        if (!refreshToken) return null;

        try {
            const res = await refreshTokenApi(refreshToken);
            localStorage.setItem(this.tokenKey, res.token);
            if (res.refresh_token) localStorage.setItem(this.refreshTokenKey, res.refresh_token);
            return res.token;
        } catch {
            this.logout();
            return null;
        }
    }

    getToken(): string | null {
        return localStorage.getItem(this.tokenKey);
    }

    getRefreshToken(): string | null {
        return localStorage.getItem(this.refreshTokenKey);
    }
}
