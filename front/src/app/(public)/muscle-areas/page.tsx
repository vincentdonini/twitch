"use client";

import PublicLayout from "@common/layouts/PublicLayout";
import {
    Box,
    Button,
    Card,
    CircularProgress,
    Link,
    Skeleton,
    Stack,
    Typography
} from "@mui/material";
import {useEffect} from "react";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import {MuscleArea} from "@common/api/domain/Muscle";
import {InfiniteData} from "@tanstack/react-query";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {useMuscleAreas} from "@common/api/hooks/useMuscleAreas";
import {generateSlug} from "@/middleware";

export default function MuscleAreasPage() {
    const {autoLoginPublic, isPublicAuthenticated, isPublicLoading} = useApiAuth();
    const {data, isLoading, fetchNextPage, hasNextPage, isFetchingNextPage} = useMuscleAreas({perPage: 10});

    useEffect(() => {
        if (!isPublicAuthenticated && !isPublicLoading) autoLoginPublic();
    }, [isPublicAuthenticated, isPublicLoading, autoLoginPublic]);

    if (!isPublicAuthenticated) return null;

    const muscleAreas: MuscleArea[] = (data as InfiniteData<ServiceResponse<MuscleArea[]>>)?.pages.flatMap(
        (page) => page.data
    ) ?? [];

    if (isPublicLoading || !isPublicAuthenticated) {
        return (
            <PublicLayout>
                <Box className="flex min-h-screen items-center justify-center">
                    <CircularProgress/>
                </Box>
            </PublicLayout>
        );
    }

    return (
        <PublicLayout>
            <Typography variant="h1" sx={{ mb: 3 }}>
                Zones musculaires
            </Typography>
            {isLoading ? (
                <Stack sx={{gap: 2}}>
                    {[...Array(5)].map((_, i) => (
                        <Skeleton key={i} variant="rounded" width="100%" height={75}/>
                    ))}
                </Stack>
            ) : muscleAreas.length === 0 ? (
                <Box sx={{textAlign: "center", my: 4}}>
                    <Typography variant="body1" color="text.secondary">
                        Aucune zone musculaire trouvée
                    </Typography>
                </Box>
            ) : (
                <Box sx={{display: "flex", flexDirection: "column", gap: 2}}>
                    {muscleAreas.map((muscleArea) => (
                        <Link
                            key={muscleArea.id}
                            href={`/muscle-areas/${generateSlug(muscleArea.name, muscleArea.id)}`}
                            underline="none"
                        >
                            <Card variant="outlined">
                                <Box sx={{p: 2}}>
                                    <Typography variant="h6">{muscleArea.name}</Typography>
                                    <Typography color="text.secondary">{muscleArea.summary}</Typography>
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
