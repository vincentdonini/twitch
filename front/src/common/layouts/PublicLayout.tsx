"use client";

import {ReactNode} from "react";
import {
    Box,
    Breadcrumbs,
    Container,
    Link,
    Typography,
} from "@mui/material";
import Footer from "@common/ui/layout/Footer";

export default function PublicLayout({children}: { children: ReactNode }) {
    return (
        <Box
            className="min-h-screen flex flex-col"
            style={{
                height: '100vh',
            }}
        >
            <Box
                component="header"
                sx={{
                    position: 'sticky',
                    alignSelf: "flex-start",
                    top: 0,
                    left: 0,
                    right: 0,
                    width: '100%',
                    zIndex: 99999,
                }}
            >
                <Box
                    className="bg-gray-900 text-white p-4"
                    sx={{
                        position: 'sticky',
                        alignSelf: "flex-start",
                        top: 0,
                        left: 0,
                        right: 0,
                        width: '100%',
                        zIndex: 99999,
                    }}
                >
                    <Container
                        maxWidth="lg"
                        sx={{
                            display: "flex",
                            direction: "row",
                            justifyContent: "space-between",
                        }}
                    >
                        <Box>
                            WODER
                        </Box>
                        <Box>
                            Header public
                        </Box>
                    </Container>
                </Box>
                <Box
                    className="bg-gray-300 text-white p-4"
                    sx={{
                        position: 'sticky',
                        alignSelf: "flex-start",
                        top: 42,
                        left: 0,
                        right: 0,
                        width: '100%',
                        zIndex: 99999,
                    }}
                >
                    <Container
                        maxWidth="lg"
                        sx={{
                            display: "flex",
                            direction: "row",
                            justifyContent: "space-between",
                        }}
                    >
                        <Breadcrumbs>
                            <Link underline="hover" color="inherit" href="/">
                                Accueil
                            </Link>
                            <Typography sx={{ color: 'text.primary' }}>
                                WODS
                            </Typography>
                        </Breadcrumbs>
                    </Container>
                </Box>
            </Box>
            <main
                className="flex flex-1 p-6"
                style={{
                    // overflow: 'auto'
                }}
            >
                <Container
                    maxWidth="lg"
                    className="flex-1"
                    style={{
                        height: '100%'
                    }}
                >
                    {children}
                </Container>
            </main>
            <Footer />
        </Box>
    );
}
