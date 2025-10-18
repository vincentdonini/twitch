"use client";

import PublicLayout from "@common/layouts/PublicLayout";
import {
    Box,
    Button,
    Card,
    Link,
    Skeleton,
    Stack,
    Typography,
} from "@mui/material";
import {useEffect} from "react";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import {generateSlug} from "@/middleware";
import {useMovements} from "@common/api/hooks/useMovements";
import {Movement} from "@common/api/domain/Movement";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {InfiniteData} from "@tanstack/react-query";

export default function MovementsPage() {
    const {autoLoginPublic, isPublicAuthenticated, isPublicLoading} = useApiAuth();
    const {data, isLoading, fetchNextPage, hasNextPage, isFetchingNextPage} = useMovements({perPage: 10});

    useEffect(() => {
        if (!isPublicAuthenticated && !isPublicLoading) autoLoginPublic();
    }, [isPublicAuthenticated, isPublicLoading, autoLoginPublic]);

    if (!isPublicAuthenticated) return null;

    const movements: Movement[] = (data as InfiniteData<ServiceResponse<Movement[]>>)?.pages.flatMap(
        (page) => page.data
    ) ?? [];

    return (
        <PublicLayout>
            {isLoading ? (
                <Stack sx={{gap: 2}}>
                    {[...Array(5)].map((_, i) => (
                        <Skeleton key={i} variant="rounded" width="100%" height={75}/>
                    ))}
                </Stack>
            ) : movements.length === 0 ? (
                <Box sx={{textAlign: "center", my: 4}}>
                    <Typography variant="body1" color="text.secondary">
                        Aucun mouvement trouvé
                    </Typography>
                </Box>
            ) : (
                <Box sx={{display: "flex", flexDirection: "column", gap: 2}}>
                    {movements.map((movement) => (
                        <Link
                            key={movement.id}
                            href={`/movements/${generateSlug(movement.name, movement.id)}`}
                            underline="none"
                        >
                            <Card variant="outlined">
                                <Box sx={{p: 2}}>
                                    <Typography variant="h6">{movement.name}</Typography>
                                    <Typography color="text.secondary">{movement.summary}</Typography>
                                </Box>
                            </Card>
                        </Link>
                    ))}

                    {hasNextPage && (
                        <Box sx={{display: "flex", justifyContent: "center", mt: 3}}>
                            <Button
                                variant="contained"
                                onClick={() => fetchNextPage()}
                                disabled={isFetchingNextPage}
                                disableElevation
                                sx={{width: '100%'}}
                            >
                                {isFetchingNextPage ? "Chargement..." : "Charger plus"}
                            </Button>
                        </Box>
                    )}
                </Box>
            )}
        </PublicLayout>
    );
}
