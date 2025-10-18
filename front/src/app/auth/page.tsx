"use client";

import {useState, useEffect} from "react";
import {useRouter} from "next/navigation";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import {Box, Button, CircularProgress, TextField, Typography, Stack} from "@mui/material";

export default function AuthPage() {
    const {loginUser, isAuthenticated, isLoading: authLoading} = useApiAuth();
    const router = useRouter();

    const [loginLoading, setLoginLoading] = useState(false);

    const [email, setEmail] = useState("admin@woder.fr");
    const [password, setPassword] = useState("Woder123!!");

    useEffect(() => {
        if (!authLoading && isAuthenticated) {
            router.replace("/admin");
        }
    }, [isAuthenticated, authLoading, router]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoginLoading(true);
        try {
            await loginUser(email, password);
            router.replace("/admin");
        } catch {
            alert("Login failed");
            setLoginLoading(false);
        }
    };

    if ((authLoading || isAuthenticated) && !loginLoading) {
        return (
            <Box className="flex min-h-screen items-center justify-center">
                <CircularProgress/>
            </Box>
        );
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-100">
            <form onSubmit={handleSubmit} className="w-full max-w-md p-6 bg-white rounded-xl">
                <Stack spacing={2}>
                    <Typography variant="h4" textAlign="center">
                        Login
                    </Typography>
                    <Stack spacing={2}>
                        <TextField
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            fullWidth
                            placeholder="Email"
                        />
                        <TextField
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                            fullWidth
                            placeholder="Password"
                        />
                        <Button
                            type="submit"
                            variant="contained"
                            color="primary"
                            fullWidth
                            loading={loginLoading}
                            disabled={loginLoading}
                            disableElevation
                        >
                            Login
                        </Button>
                    </Stack>
                </Stack>
            </form>
        </div>
    );
}
