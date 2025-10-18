"use client";

import {useEffect} from "react";
import {useRouter} from "next/navigation";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import {Box} from '@mui/material';
import SideMenu from "@common/ui/layout/SideMenu";
import AppNavbar from "@common/ui/layout/AppNavBar";
import Header from "@common/ui/layout/Header";
import MainGrid from "@common/ui/layout/MainGrid";
import Copyright from '@common/ui/layout/Copyright';

export default function AdminLayout({children}: { children: React.ReactNode }) {
    const {isAuthenticated, isLoading} = useApiAuth();
    const router = useRouter();

    useEffect(() => {
        if (!isLoading && !isAuthenticated) {
            router.push("/auth");
        }
    }, [isAuthenticated, isLoading, router]);

    if (isLoading || !isAuthenticated) {
        return null;
    }

    return (
        <>
            {/*<CssBaseline enableColorScheme />*/}
            <Box
                sx={{
                    height: "100vh",
                    display: 'flex',
                    flexGrow: 1,
                }}
            >
                <SideMenu/>
                <AppNavbar/>
                <Box
                    component="main"
                    sx={{
                        display: "flex",
                        flexDirection: "column",
                        flexGrow: 1,
                    }}
                >
                    <Header/>
                    <MainGrid>
                        {children}
                    </MainGrid>
                    <Copyright/>
                </Box>
            </Box>
        </>
    );
}
